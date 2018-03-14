<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Grid_Renderer_Prodcat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $text = array();
        $catIds = $row->getCatIds();
        $allCats = Mage::getSingleton('mageworx_downloads/system_config_source_categories')->getOptionArray();

        if ($catIds && is_string($catIds)) {
            foreach (explode(',', $catIds) as $id) {
                if (isset($allCats[$id])) {
                    $text[] = str_replace('&nbsp;', '', $allCats[$id]);
                }
            }
        }

        return implode(', ', $text);
    }
}