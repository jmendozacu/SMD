<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    private $_helper;

    /**
     * Set blockGroup
     * Set controller
     * Set mode
     * Set headerText
     *
     * @throws Exception
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('gallery');
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_gallery_tag_category';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', $this->_helper->__('Save Gallery Tag Category'));
        $this->_updateButton('delete', 'label', $this->_helper->__('Delete Gallery Tag Category'));

        $this->_addButton('saveandcontinue', array(
            'label'     => $this->_helper->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        }
    }

    public function getHeaderText()
    {
        if(Mage::registry('current_gallery_tag_category') && Mage::registry('current_gallery_tag_category')->getEntityId())
        {
            return $this->_helper->__("Edit Gallery Tag Category '%s'", $this->escapeHtml(Mage::registry('current_gallery_tag_category')->getTitle()));
        }
        else
        {
            return $this->_helper->__('Add New Gallery Tag Category');
        }
    }

}