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

class Blackbird_ContentManager_Model_Admin_Resource_Acl extends Mage_Admin_Model_Resource_Acl
{
    
    /**
     * Load ACL for the user
     *
     * @return Mage_Admin_Model_Acl
     */
    public function loadAcl()
    {
        $acl = Mage::getModel('admin/acl');

        //get cached ACL
        $cacheGroup = 'contentmanager_acl';
        $useCache = Mage::app()->useCache($cacheGroup);
        $aclCache = null;
        if(true === $useCache)
        {
            $cache = Mage::app()->getCache();
            $aclCache = $cache->load('contentmanager_acl');
            if(is_string($aclCache))
            {
                $aclCache = @unserialize($aclCache);
            }
        }
        
        if(!$aclCache || !($aclCache instanceof Mage_Admin_Model_Acl))
        {
            Mage::getSingleton('admin/config')->loadAclResources($acl);

            //Blackbird - Add content ACL for CT extension
            $collection = Mage::getModel('contentmanager/contenttype')->getCollection();
            $parentName = 'admin/contentmanager';

            $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_everything'), $parentName);
            $stores = Mage::getModel('core/store')->getCollection();

            foreach($collection as $contentType)
            {
                $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_'.$contentType->getIdentifier()), $parentName);

                //loop stores
                $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_'.$contentType->getIdentifier().'_view_0'), $parentName);
                $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_'.$contentType->getIdentifier().'_edit_0'), $parentName);
                foreach($stores as $store)
                {
                    $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_'.$contentType->getIdentifier().'_view_'.$store->getId()), $parentName);
                    $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/content_'.$contentType->getIdentifier().'_edit_'.$store->getId()), $parentName);
                }
            }

            //permissions menu
            $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/manage_menu_0'), $parentName);
            foreach($stores as $store)
            {
                $acl->add(Mage::getModel('admin/acl_resource', $parentName.'/manage_menu_'.$store->getId()), $parentName);
            }


            //Blackbird - End Add content ACL for CT extension

            $roleTable   = $this->getTable('admin/role');
            $ruleTable   = $this->getTable('admin/rule');
            $assertTable = $this->getTable('admin/assert');

            $adapter = $this->_getReadAdapter();

            $select = $adapter->select()
                ->from($roleTable)
                ->order('tree_level');

            $rolesArr = $adapter->fetchAll($select);

            $this->loadRoles($acl, $rolesArr);

            $select = $adapter->select()
                ->from(array('r' => $ruleTable))
                ->joinLeft(
                    array('a' => $assertTable),
                    'a.assert_id = r.assert_id',
                    array('assert_type', 'assert_data')
                );

            $rulesArr = $adapter->fetchAll($select);

            $this->loadRules($acl, $rulesArr);
            
            //save cache
            if(true === $useCache)
            {
                $cache->save(serialize($acl), "contentmanager_acl", array("CONTENTMANAGER_ACL_CACHE"), 7*24*60*60); //7 days cache
            }
        }
        else
        {
            $acl = $aclCache;
        }
     
        return $acl;
    }

}
