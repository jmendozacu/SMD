<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_System_Config_Source_Categories extends Varien_Object
{
    private $_options = array();

    public function getOptionArray()
    {
        if (count($this->_options)) {
            return $this->_options;
        }

        foreach (Mage::app()->getWebsites() as $website) {

            if (is_object($website->getDefaultStore())) {
                $rootId = $website->getDefaultStore()->getRootCategoryId();
                $rootCat = Mage::getModel('catalog/category')->load($rootId);
                $this->_options[$rootId] = $rootCat->getName();
                $this->getChildCats($rootCat, 0);
            }
        }

        return $this->_options;
    }

    protected function getChildCats($cat, $level)
    {
        if ($children = $cat->getChildren()) {
            $level++;
            $children = explode(',', $children);
            foreach ($children as $childId) {
                $childCat = Mage::getModel('catalog/category')->load($childId);
                $this->_options[$childId] = str_repeat('&nbsp;&nbsp;&nbsp;', $level) . $childCat->getName();
                if ($childCat->getChildren()) {
                    $this->getChildCats($childCat, $level);
                }
            }
        }
    }
}
