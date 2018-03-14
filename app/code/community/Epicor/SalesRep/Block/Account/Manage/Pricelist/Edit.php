<?php

/**
 * List edit form setup
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * @var Epicor_Lists_Model_List
     */
    private $_list;

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_account_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('epicor_lists')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            ), -100);

        $this->_updateButton('save', 'label', Mage::helper('epicor_lists')->__('Save'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Gets the current List
     * 
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->_list) {
            $this->_list = Mage::registry('list');
        }
        return $this->_list;
    }

    /**
     * Sets the header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $list = $this->getList();
        /* @var $list Epicor_Lists_Model_List */
        if ($list->getId()) {
            $title = $list->getTitle();
            return Mage::helper('epicor_lists')->__('List: %s', $title);
        } else {
            return Mage::helper('epicor_lists')->__('New List');
        }
    }

}
