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

class Blackbird_ContentManager_Block_Adminhtml_Review_Renderer_Content extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $_indexColumn = 'title'; // Will replace $this->getColumn()->getIndex() which are "entity_id"
    
    public function render(Varien_Object $row)
    {
        $stores = Mage::getModel('core/store')->getCollection();
        $content = Mage::getModel('contentmanager/content')->setStoreId(0)->load($row->getEntityId());
        $contentType = $content->getContentType();

        if($content->existsForStore(0))
        {
            $flag_img = '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'contenttype/flags/world.png'.'" /> ';   
            if($content->getData($this->_indexColumn) !== null)
            {
                echo $flag_img.$content->getData($this->_indexColumn).' ('.$contentType->getTitle().')'.'<br />';
            }
        }
                    
        foreach($stores as $store)
        {
            $content = Mage::getModel('contentmanager/content')->setStoreId($store->getId())->load($row->getEntityId());
            
            if($content->existsForStore($store->getId()))
            {
                $flag = Mage::getModel('contentmanager/flag')->load($store->getId());
                $flag_img = '<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'contenttype/flags/'.$flag->getValue().'" /> ';

                if($content->getData($this->getColumn()->getIndex()) !== null)
                {
                    echo $flag_img.$content->getData($this->_indexColumn).' ('.$contentType->getTitle().')'.'<br />';
                }
            }
        }
    }
}

