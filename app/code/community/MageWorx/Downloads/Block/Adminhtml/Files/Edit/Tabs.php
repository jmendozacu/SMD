<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('downloads_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->_getHelper()->__('General Information'));
    }

    protected function _beforeToHtml()
    {
        $helper = $this->_getHelper();

        $assignProducts = Mage::app()->getRequest()->getActionName() == 'assignProducts';

        if (!$assignProducts) {
            $this->addTab(
                'general_tab', array(
                'label' => $helper->__('File'),
                'title' => $helper->__('File'),
                'content' => $this->getLayout()->createBlock('mageworx_downloads/adminhtml_files_edit_tab_general')->toHtml(),
                'active' => true,
                )
            );
        }

        $this->addTab(
            'product_tab', array(
            'label' => $helper->__('Products'),
            'title' => $helper->__('Products'),
            'content' => $this->getLayout()->createBlock('mageworx_downloads/adminhtml_files_edit_tab_product')->toHtml(),
            'active' => $assignProducts ? true : false
            )
        );

        $this->addTab(
            'downloads_tab', array(
            'label' => $helper->__('Downloads'),
            'title' => $helper->__('Downloads'),
            'content' =>
                $this->getLayout()->createBlock('mageworx_downloads/adminhtml_files_edit_tab_downloads_guests')->toHtml()
                . $this->getLayout()->createBlock('mageworx_downloads/adminhtml_files_edit_tab_downloads')->toHtml(),
            'active' => false
            )
        );

        return parent::_beforeToHtml();
    }

    private function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

}
