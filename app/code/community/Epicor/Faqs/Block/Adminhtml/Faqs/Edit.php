<?php

/**
 * F.A.Q. adminhtml edit form container
 * 
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 *
 */
class Epicor_Faqs_Block_Adminhtml_Faqs_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Initialize edit form container
     *
     */
    public function __construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'epicor_faqs';
        $this->_controller = 'adminhtml_faqs';

        parent::__construct();
        //Verifying save and delete permissions and adding buttons if permitted
        if (Mage::helper('epicor_faqs')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('epicor_faqs')->__('Save F.A.Q.'));
        } else {
            $this->_removeButton('save');
        }

        if (Mage::helper('epicor_faqs')->isActionAllowed('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('epicor_faqs')->__('Delete F.A.Q.'));
        } else {
            $this->_removeButton('delete');
        }
        //JS function to toggle WYSIWYG editor when clicked, doesn't do anything in IE
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }
        ";
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText() {
        $model = Mage::helper('epicor_faqs')->getFaqsItemInstance();
        if ($model->getId()) {
            return Mage::helper('epicor_faqs')->__("Edit Faqs Item '%s'", $this->escapeHtml($model->getQuestion()));
        } else {
            return Mage::helper('epicor_faqs')->__('New Faqs Item');
        }
    }

    /**
     * Prepare WYSIWYG config
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

}
