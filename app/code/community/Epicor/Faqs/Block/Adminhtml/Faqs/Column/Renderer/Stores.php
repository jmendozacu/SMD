<?php

/**
 * Renderer for Sites > Stores column, shows list of stores for the site
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_Faqs_Block_Adminhtml_Faqs_Column_Renderer_Stores extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        try {
            $storeIds = explode(',', $row->getStores());
            $websites = array();
            foreach ($storeIds as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $website = $store->getWebsite();
                $websites[$website->getId()]['Name'] = $website->getName();
                $websites[$website->getId()]['Stores'][$storeId] = $store->getName();
            }
            
            $output = '<dl>';
            foreach ($websites as $websiteId => $website) {
                $output .= '<dt>Website:</dt>';
                $output .= '<dd style="margin-left:10px;">' . $website['Name'] . '</dd>';
                $output .= '<dt style="margin-left:10px;">Stores:</dt>';
                foreach ($website['Stores'] as $storeId => $store) {
                    $output .= '<dd style="margin-left:20px;">' . $store . '</dd>';
                }
            }
        } catch (Exception $e) {
            $output = '<dl><dt>Store Not Found. Store may have been deleted</dt>';
        }
        $output .= '</dl>';

        return $output;
    }

}
