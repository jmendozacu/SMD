<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'mageworx_downloads';
        $this->_controller = 'adminhtml_files';
        $helper = Mage::helper('mageworx_downloads');

        parent::__construct();

        $this->_updateButton(
            'save', '', array(
            'label' => $helper->__('Save'),
            'onclick' => 'saveDownloadsForm()',
            'class' => 'save',
            'sort_order' => 30
            ), 1
        );

        $this->_updateButton(
            'delete', '', array(
            'label' => $helper->__('Delete'),
            'onclick' => "deleteConfirm('{$helper->__('Are you sure you want to do this?')}', '{$this->getUrl('*/*/delete', array('id' => (int) $this->getRequest()->getParam('id')))}')",
            'class' => 'delete',
            'sort_order' => 10
            )
        );

        if (Mage::app()->getRequest()->getActionName() != 'multiupload' && Mage::app()->getRequest()->getActionName() != 'assignProducts') {
            $this->_addButton(
                'saveandcontinue', array(
                'label' => $helper->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                'sort_order' => 20
                ), -100
            );
        }

        $this->_formScripts[] = "
        	function saveDownloadsForm() {
        		applySelectedProducts('save')
            }
            function saveAndContinueEdit() {
                applySelectedProducts('saveandcontinue')
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('downloads_data') && Mage::registry('downloads_data')->getId()) {
            return Mage::helper('mageworx_downloads')->__("Edit File '%s'", $this->htmlEscape(Mage::registry('downloads_data')->getName()));
        } else {
            return Mage::helper('mageworx_downloads')->__('Upload File');
        }
    }

}
