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

class Blackbird_ContentManager_Model_Resource_ContentType_Option extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Define main table and initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option', 'option_id');
    }

    /**
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
     * Save titles
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Blackbird_ContentManager_Model_Resource_Product_Option
     */
    protected function _saveValueTitles(Mage_Core_Model_Abstract $object)
    {
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        $titleTable = $this->getTable('contentmanager/contenttype_option_title');

        //title
        if (!$object->getData('scope', 'title')) {
            $statement = $readAdapter->select()
                ->from($titleTable)
                ->where('option_id = ?', $object->getId())
                ->where('store_id  = ?', Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID);

            if ($readAdapter->fetchOne($statement)) {
                if ($object->getStoreId() == '0') {
                    $data = $this->_prepareDataForTable(
                        new Varien_Object(
                            array(
                                'title' => $object->getTitle()
                            )
                        ),
                        $titleTable
                    );

                    $writeAdapter->update(
                        $titleTable,
                        $data,
                        array(
                            'option_id = ?' => $object->getId(),
                            'store_id  = ?' => Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID
                        )
                    );
                }
            } else {
                $data = $this->_prepareDataForTable(
                    new Varien_Object(
                        array(
                            'option_id' => $object->getId(),
                            'store_id'  => Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID,
                            'title'     => $object->getTitle()
                        )
                    ),
                    $titleTable
                );

                $writeAdapter->insert($titleTable, $data);
            }
        }

        if ($object->getStoreId() != '0' && !$object->getData('scope', 'title')) {
            $statement = $readAdapter->select()
                ->from($titleTable)
                ->where('option_id = ?', $object->getId())
                ->where('store_id  = ?', $object->getStoreId());

            if ($readAdapter->fetchOne($statement)) {
                $data = $this->_prepareDataForTable(
                    new Varien_Object(
                        array(
                            'title' => $object->getTitle()
                        )
                    ),
                    $titleTable
                );

                $writeAdapter->update(
                    $titleTable,
                    $data,
                    array(
                        'option_id = ?' => $object->getId(),
                        'store_id  = ?' => $object->getStoreId()
                    )
                );
            } else {
                $data = $this->_prepareDataForTable(
                    new Varien_Object(
                        array(
                            'option_id' => $object->getId(),
                            'store_id'  => $object->getStoreId(),
                            'title'     => $object->getTitle()
                        )
                    ),
                    $titleTable
                );
                $writeAdapter->insert($titleTable, $data);
            }
        } elseif ($object->getData('scope', 'title')) {
            $writeAdapter->delete(
                $titleTable,
                array(
                    'option_id = ?' => $object->getId(),
                    'store_id  = ?' => $object->getStoreId()
                )
            );
        }
    }

    /**
     * Delete titles
     *
     * @param int $optionId
     * @return Blackbird_ContentManager_Model_Resource_Product_Option
     */
    public function deleteTitles($optionId)
    {
        $this->_getWriteAdapter()->delete(
            $this->getTable('contentmanager/contenttype_option_title'),
            array(
                'option_id = ?' => $optionId
            )
        );

        return $this;
    }
    
    public function updateAttributeId($identifier, $attribute_id)
    {
        $table  = Mage::getModel('contentmanager/contenttype_option')->getResource()->getTable('contentmanager/contenttype_option');
        $this->_getWriteAdapter()->update($table, array('attribute_id' => $attribute_id), 'identifier = "'.$identifier.'"');
    }
}
