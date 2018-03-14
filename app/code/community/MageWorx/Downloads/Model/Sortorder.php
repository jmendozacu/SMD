<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Sortorder
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('mageworx_downloads')->__('Alphabetical')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('mageworx_downloads')->__('Upload Date')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('mageworx_downloads')->__('Size')
            ),
            array(
                'value' => 4,
                'label' => Mage::helper('mageworx_downloads')->__('Downloads')
            ),
        );
    }
}
