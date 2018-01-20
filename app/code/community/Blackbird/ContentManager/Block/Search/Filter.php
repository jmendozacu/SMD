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

class Blackbird_ContentManager_Block_Search_Filter extends Mage_Core_Block_Template
{
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if(!$this->getTemplate())
        {
            $this->setTemplate('contenttype/search/filter.phtml');
        }
        
        return $this;
    }

    public function getCollection()
    {
        $allCtId = Mage::registry('all_ct_id');
        
        $contentTypeCollection = null;
        if(is_array($allCtId) && count($allCtId) > 0)
        {
            $contentTypeCollection = Mage::getModel('contentmanager/contenttype')
                    ->getCollection()
                    ->addFieldToSelect('title')
                    ->addFieldToSelect('identifier')
                    ->addFieldToSelect('ct_id')
                    ->addFieldToFilter('ct_id', array('in', $allCtId));
        }
        
        return $contentTypeCollection;
    }

    public function getFilteredUrl($params=array(), $forceBaseUrl = '*/*/*')
    {
        $urlParams = array();
        $currentCt = Mage::app()->getRequest()->getParam('ct');
        $currentCt = explode(',', $currentCt);
        if(!in_array($params['ct'], $currentCt))
        {
            $currentCt[] = $params['ct'];
        }
        else
        {
            unset($currentCt[array_search($params['ct'], $currentCt)]);
        }
        if(isset($currentCt[0]) && $currentCt[0] === '') unset($currentCt[0]);
        
        if(count($currentCt) == 0)
        {
            $params['ct'] = null;
        }
        else
        {
            $params['ct'] = implode(',', $currentCt);
        }
        
        if($forceBaseUrl == '*/*/*')
        {
            $urlParams['_current']  = true;
            $urlParams['_escape']   = true;
            $urlParams['_use_rewrite']   = true;
        }
        $urlParams['_query']    = $params;
        return $this->getUrl($forceBaseUrl, $urlParams);
    }
    
    public function isOptionActive($option)
    {
        $currentCt = Mage::app()->getRequest()->getParam('ct');
        $currentCt = explode(',', $currentCt);
        
        return in_array($option, $currentCt);
    }
    
    public function getTotalByCtId($ctid)
    {
        $countCtId = Mage::registry('count_ct_id');
        
        return $countCtId[$ctid];
    }
    
}