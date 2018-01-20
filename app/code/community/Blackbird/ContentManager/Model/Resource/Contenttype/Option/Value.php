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

class Blackbird_ContentManager_Model_Resource_ContentType_Option_Value extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option_type_value', 'option_type_id');
    }

    /**
     * Proceeed operations after object is saved
     * Save options store data
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        //$this->_saveValuePrices($object);
        $this->_saveValueTitles($object);

        return parent::_afterSave($object);
    }

    /**
     * Save option value title data
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _saveValueTitles(Mage_Core_Model_Abstract $object)
    {
        $titleTable = $this->getTable('contentmanager/contenttype_option_type_title');

        if (!$object->getData('scope', 'title')) {
            $select = $this->_getReadAdapter()->select()
                ->from($titleTable, array('option_type_id'))
                ->where('option_type_id = ?', (int)$object->getId())
                ->where('store_id = ?', 0);
            $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

            if ($optionTypeId) {
                if ($object->getStoreId() == '0') {
                    $where = array(
                        'option_type_id = ?'    => (int)$optionTypeId,
                        'store_id = ?'          => Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID
                    );
                    $bind  = array(
                        'title' => $object->getTitle()
                    );
                    $this->_getWriteAdapter()->update($titleTable, $bind, $where);
                }
            } else {
                $bind  = array(
                    'option_type_id'    => (int)$object->getId(),
                    'store_id'          => Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID,
                    'title'             => $object->getTitle()
                );
                $this->_getWriteAdapter()->insert($titleTable, $bind);
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $select = $this->_getReadAdapter()->select()
                ->from($titleTable, array('option_type_id'))
                ->where('option_type_id = ?', (int)$object->getId())
                ->where('store_id = ?', (int)$object->getStoreId());
            $optionTypeId = $this->_getReadAdapter()->fetchOne($select);

            if ($optionTypeId) {
                $bind  = array(
                    'title' => $object->getTitle()
                );
                $where = array(
                    'option_type_id = ?'    => (int)$optionTypeId,
                    'store_id = ?'          => (int)$object->getStoreId()
                );
                $this->_getWriteAdapter()->update($titleTable, $bind, $where);
            } else {
                $bind  = array(
                    'option_type_id'    => (int)$object->getId(),
                    'store_id'          => (int)$object->getStoreId(),
                    'title'             => $object->getTitle()
                );
                $this->_getWriteAdapter()->insert($titleTable, $bind);
            }
        } else if ($object->getData('scope', 'title')) {
            $where = array(
                'option_type_id = ?'    => (int)$optionTypeId,
                'store_id = ?'          => (int)$object->getStoreId()
            );
            $this->_getWriteAdapter()->delete($titleTable, $where);
        }
    }

    /**
     * Delete values by option id
     *
     * @param int $optionId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Value
     */
    public function deleteValue($optionId)
    {
        $statement = $this->_getReadAdapter()->select()
            ->from($this->getTable('contentmanager/contenttype_option_type_value'))
            ->where('option_id = ?', $optionId);

        $rowSet = $this->_getReadAdapter()->fetchAll($statement);

        foreach ($rowSet as $optionType) {
            $this->deleteValues($optionType['option_type_id']);
        }

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array(
                'option_id = ?' => $optionId,
            )
        );

        return $this;
    }

    /**
     * Delete values by option type
     *
     * @param int $optionTypeId
     */
    public function deleteValues($optionTypeId)
    {
        $condition = array(
            'option_type_id = ?' => $optionTypeId
        );

        $this->_getWriteAdapter()->delete(
            $this->getTable('contentmanager/contenttype_option_type_title'),
            $condition
        );
    }
}
