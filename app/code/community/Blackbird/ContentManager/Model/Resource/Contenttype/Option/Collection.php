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

class Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option');
    }

    /**
     * Adds title, price & price_type attributes to result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function getOptions($storeId)
    {
        $this/*->addPriceToResult($storeId)*/
             ->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add title to result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function addTitleToResult($storeId)
    {
        $contentTypeOptionTitleTable = $this->getTable('contentmanager/contenttype_option_title');
        $adapter        = $this->getConnection();
        $titleExpr      = 'IF('.
            'store_option_title.title IS NULL, '.
            'default_option_title.title, '.
            'store_option_title.title)'
        ;

        $this->getSelect()
            ->join(array('default_option_title' => $contentTypeOptionTitleTable),
                'default_option_title.option_id = main_table.option_id',
                array('default_title' => 'title'))
            ->joinLeft(
                array('store_option_title' => $contentTypeOptionTitleTable),
                'store_option_title.option_id = main_table.option_id AND '
                    . $adapter->quoteInto('store_option_title.store_id = ?', $storeId),
                array(
                    'store_title'   => 'title',
                    'title'         => $titleExpr
                ))
            ->where('default_option_title.store_id = ?', Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add value to result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function addValuesToResult($storeId = null)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $optionIds = array();
        foreach ($this as $option) {
            $optionIds[] = $option->getId();
        }
        if (!empty($optionIds)) {
            /** @var $values Blackbird_ContentManager_Model_Option_Value_Collection */
            $values = Mage::getModel('contentmanager/contenttype_option_value')
                ->getCollection()
                ->addTitleToResult($storeId)
                //->addPriceToResult($storeId)
                ->addOptionToFilter($optionIds)
                ->setOrder('sort_order', self::SORT_ORDER_ASC)
                ->setOrder('title', self::SORT_ORDER_ASC);

            foreach ($values as $value) {
                $optionId = $value->getOptionId();
                if($this->getItemById($optionId)) {
                    $this->getItemById($optionId)->addValue($value);
                    $value->setOption($this->getItemById($optionId));
                }
            }
        }

        return $this;
    }

    /**
     * Add contenttype_id filter to select
     *
     * @param array|Blackbird_ContentManager_Model_ContentType|int $contentType
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function addContentTypeToFilter($contentType)
    {
        if (empty($contentType)) {
            $this->addFieldToFilter('ct_id', '');
        } elseif (is_array($contentType)) {
            $this->addFieldToFilter('ct_id', array('in' => $contentType));
        } elseif ($contentType instanceof Blackbird_ContentManager_Model_Contenttype) {
            $this->addFieldToFilter('ct_id', $contentType->getId());
        } else {
            $this->addFieldToFilter('ct_id', $contentType);
        }

        return $this;
    }

    /**
     * Add is_required filter to select
     *
     * @param bool $required
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function addRequiredFilter($required = true)
    {
        $this->addFieldToFilter('main_table.is_require', (string)$required);
        return $this;
    }

    /**
     * Add filtering by option ids
     *
     * @param mixed $optionIds
     * @return Blackbird_ContentManager_Model_Resource_Eav_Mysql4_ContentType_Option_Collection
     */
    public function addIdsToFilter($optionIds)
    {
        $this->addFieldToFilter('main_table.option_id', $optionIds);
        return $this;
    }

    /**
     * Call of protected method reset
     *
     * @return Blackbird_ContentManager_Model_Resource_ContentType_Option_Collection
     */
    public function reset()
    {
        return $this->_reset();
    }
}
