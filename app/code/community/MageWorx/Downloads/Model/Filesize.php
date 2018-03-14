<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Filesize
{
    const FILE_SIZE_PRECISION_AUTO = 1;
    const FILE_SIZE_PRECISION_KILO = 2;
    const FILE_SIZE_PRECISION_MEGA = 3;

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::FILE_SIZE_PRECISION_AUTO,
                'label' => Mage::helper('mageworx_downloads')->__('Auto')
            ),
            array(
                'value' => self::FILE_SIZE_PRECISION_KILO,
                'label' => Mage::helper('mageworx_downloads')->__('Kilobytes')
            ),
            array(
                'value' => self::FILE_SIZE_PRECISION_MEGA,
                'label' => Mage::helper('mageworx_downloads')->__('Megabytes')
            ),
        );
    }
}
