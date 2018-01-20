<?php

/**
 * Renderer for Sites > Stores column, shows list of stores for the site
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Sites_Column_Renderer_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {

        $output = '<dl>';
        if ($row->getIsDefault()) {
            $defaultStore = Mage::app()->getDefaultStoreView();
            $website = $defaultStore->getWebsite();
            $output .= '<dt>Website:</dt>';
            if ($website) {
                $output .= '<dd>' . $website->getName() . '</dd>';
            }
            $output .= '<dt>Stores:</dt>';
            foreach ($website->getStores() as $store) {
                if (!in_array($store->getId(), $row->getIgnoreStores()) || $store->getId() == $defaultStore->getId()) {
                    $output .= '<dd>' . $store->getName() . '</dd>';
                }
            }
        } elseif ($row->getIsWebsite()) {
            try {
                $output .= '<dt>Website:</dt>';
                $website = Mage::app()->getWebsite($row->getChildId());
                if ($website) {
                    $output .= '<dd>' . $website->getName() . '</dd>';
                }
                $output .= '<dt>Stores:</dt>';
                foreach ($this->_getStores() as $store) {
                    if (!in_array($store->getId(), $row->getIgnoreStores()) && $store->getWebsiteId() == $row->getChildId()) {
                        $output .= '<dd>' . $store->getName() . '</dd>';
                    }
                }
            } catch (Exception $e) {
                $output = '<dl><dt>Website Not Found. Website may have been Deleted</dt>';
            }
        } else {
            try {
                $store = Mage::app()->getStore($row->getChildId());
                $website = $store->getWebsite();
                $output .= '<dt>Website:</dt>';
                if ($website) {
                    $output .= '<dd>' . $website->getName() . '</dd>';
                }
                $output .= '<dt>Stores:</dt>';
                if ($store) {
                    $output .= '<dd>' . $store->getName() . '</dd>';
                }
            } catch (Exception $e) {
                $output = '<dl><dt>Store Not Found. Store may have been deleted</dt>';
            }
        }

        $output .= '</dl>';
        return $output;
    }

    private function _getStores()
    {
        if (!Mage::registry('stores_list')) {
            $storeModel = Mage::getSingleton('adminhtml/system_store');
            /* @var $storeModel Mage_Adminhtml_Model_System_Store */
            $stores = $storeModel->getStoreCollection();
            Mage::register('stores_list', $stores);
        } else {
            $stores = Mage::registry('stores_list');
        }

        return $stores;
    }

}
