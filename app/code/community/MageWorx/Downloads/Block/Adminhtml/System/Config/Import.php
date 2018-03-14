<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_System_Config_Import extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_getAddRowButtonHtml();
    }

    protected function _getAddRowButtonHtml($sku = '')
    {
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/mageworx_downloads_files/import/');
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__('Import'))
            ->setOnClick("window.open('" . $url . "')")
            ->toHtml();
    }

}