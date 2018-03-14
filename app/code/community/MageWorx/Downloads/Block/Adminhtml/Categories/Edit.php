<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Categories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'mageworx_downloads';
        $this->_controller = 'adminhtml_categories';

        $this->_updateButton('save', 'label', $this->_getHelper()->__('Save Category'));


        $confirmMessage = 'All files inside will be moved to Default Category. Are you sure you want to proceed?';
        $confirmMessage = $this->_getHelper()->__($confirmMessage);
        $urlForDelete   = $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id')));

        $this->_updateButton(
            'delete', '', array(
            'label' => $this->_getHelper()->__('Delete Category'),
            'onclick' => 'deleteConfirm(\'' . $confirmMessage . '\', \'' . $urlForDelete . '\')',
            'class' => 'delete',
            'sort_order' => 10
            )
        );

        $this->_addButton(
            'saveandcontinue', array(
            'label' => $this->_getHelper()->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            'sort_order' => 20
            ), -100
        );

        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
    }

    private function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

    public function getHeaderText()
    {
        if (Mage::registry('downloads_data') && Mage::registry('downloads_data')->getId()) {
            $title = Mage::registry('downloads_data')->getTitle();
            return $this->_getHelper()->__("Edit Category '%s'", $this->htmlEscape($title));
        } else {
            return $this->_getHelper()->__('Add Category');
        }
    }
}
