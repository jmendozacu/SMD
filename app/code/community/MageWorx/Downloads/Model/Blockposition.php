<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Blockposition
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('mageworx_downloads')->__('Before Product Description')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('mageworx_downloads')->__('After Product Description')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('mageworx_downloads')->__('Add Tab (if supported by theme)')
            ),
        );
    }
}
