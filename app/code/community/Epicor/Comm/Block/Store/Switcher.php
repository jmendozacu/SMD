<?php

/**
 * Store Switcher
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Store_Switcher extends Mage_Core_Block_Template
{

    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    public function getImageSize()
    {
        return Mage::getStoreConfig('Epicor_Comm/brands/brand_image');
    }

    public function getBrandImageUrl($brandImage)
    {
        $urlBase = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'brandimage/';
        
        return $urlBase. $this->_processBrandImage($brandImage);
    }

    public function _processBrandImage($brandImage)
    {
        $size = Mage::getStoreConfig('Epicor_Comm/brands/brand_image_size');
        $brandImageFileName = $size . 'x' . $size . $brandImage;
        
        //process brand image
        $basePath = Mage::getBaseDir('media') . DS . 'brandimage' . DS;
        
        if (!file_exists($basePath . $brandImageFileName)) {
            try {
                
                $_image = new Varien_Image($basePath . $brandImage);
                $_image->constrainOnly(true);
                $_image->keepAspectRatio(true);
                $_image->keepFrame(true);
                $_image->keepTransparency(true);
                $_image->resize($size, $size);
                $_image->save($basePath, $brandImageFileName);
                
            } catch (Exception $e) {
                Mage::log('--- error saving uploaded brand image ---');
                Mage::log($e);
            }
        }
        
        return $brandImageFileName;
    }

}
