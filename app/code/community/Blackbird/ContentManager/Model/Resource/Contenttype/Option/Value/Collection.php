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

class Blackbird_ContentManager_Model_Resource_ContentType_Option_Value_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option_value');
    }

    /**
     * Add price, title to result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value_Collection
     */
    public function getValues($storeId)
    {
        $this->addTitleToResult($storeId);

        return $this;
    }

    /**
     * Add titles to result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value_Collection
     */
    public function addTitlesToResult($storeId)
    {
        $adapter = $this->getConnection();
        $optionTitleTable     = $this->getTable('contentmanager/contenttype_option_type_title');

        $titleExpr = '('.
            'store_value_title.title IS NULL, '.
            'default_value_title.title, '.
            'store_value_title.title)'
        ;

        $joinExprTitle = 'store_value_title.option_type_id = main_table.option_type_id AND '
                       . $adapter->quoteInto('store_value_title.store_id = ?', $storeId);

        $this->getSelect()
            ->join(
                array('default_value_title' => $optionTitleTable),
                'default_value_title.option_type_id = main_table.option_type_id',
                array('default_title' => 'title')
            )
            ->joinLeft(
                array('store_value_title' => $optionTitleTable),
                $joinExprTitle,
                array(
                    'store_title' => 'title',
                    'title'       => $titleExpr)
            )
            ->where('default_value_title.store_id = ?', Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add title result
     *
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value_Collection
     */
    public function addTitleToResult($storeId)
    {
        $optionTitleTable = $this->getTable('contentmanager/contenttype_option_type_title');
        $titleExpr = 'IF(store_value_title.title IS NULL, default_value_title.title, store_value_title.title)';

        $joinExpr = 'store_value_title.option_type_id = main_table.option_type_id AND '
                  . $this->getConnection()->quoteInto('store_value_title.store_id = ?', $storeId);
        $this->getSelect()
            ->join(
                array('default_value_title' => $optionTitleTable),
                'default_value_title.option_type_id = main_table.option_type_id',
                array('default_title' => 'title')
            )
            ->joinLeft(
                array('store_value_title' => $optionTitleTable),
                $joinExpr,
                array(
                    'store_title'   => 'title',
                    'title'         => $titleExpr
                )
            )
            ->where('default_value_title.store_id = ?', Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID);

        return $this;
    }

    /**
     * Add option filter
     *
     * @param array $optionIds
     * @param int $storeId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value_Collection
     */
    public function getValuesByOption($optionIds, $storeId = null)
    {
        if (!is_array($optionIds)) {
            $optionIds = array($optionIds);
        }

        return $this->addFieldToFilter('main_table.option_type_id', array('in' => $optionIds));
    }

    /**
     * Add option to filter
     *
     * @param array|Blackbird_ContentManager_Model_Product_Option|int $option
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value_Collection
     */
    public function addOptionToFilter($option)
    {
        if (empty($option)) {
            $this->addFieldToFilter('option_id', '');
        } elseif (is_array($option)) {
            $this->addFieldToFilter('option_id', array('in' => $option));
        } elseif ($option instanceof Blackbird_ContentManager_Model_ContentType_Option) {
            $this->addFieldToFilter('option_id', $option->getId());
        } else {
            $this->addFieldToFilter('option_id', $option);
        }

        return $this;
    }
}
