<?php

/**
 * List edit form setup
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * @var Epicor_Lists_Model_List
     */
    private $_list;

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('epicor_lists')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'sac\')',
            'class' => 'save',
            ), -100);
        $this->_updateButton('save', 'onclick', 'saveAndContinueEdit(\'save\');');
        
        $params = array(
            'id' => $this->getRequest()->getParam('id')
        );
        
        $checkUrl = $this->getUrl('*/*/orphanCheck', $params);
        $alertMsg = $this->__('Invalid Option: One or more ERP Accounts must be chosen if "Exclude selected ERP Accounts" is not ticked');
        $proceedMsg = $this->__('Do you wish to Proceed?');
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
          
            function saveAndContinueEdit(type){
                // if ERP Account tab loaded, then analyse changes
                if($('erp_account_link_type')){
                    var formData = $('edit_form').serialize(true);
                    performAjax('" . $checkUrl . "', 'post', formData, function (data) {                        
                        var json = data.responseText.evalJSON();  
                        var displayMessage = json.message;
                        if(json.type == 'success'){
                            if (json.exlusionerror) {
                                json.message = json.message + \"\\n\\n\" + '$alertMsg'
                                alert(json.message);
                                return false;
                            } else {
                                json.message = json.message + \"\\n\\n\" + '$proceedMsg';
                                if (!window.confirm(json.message)) {
                                    return false;
                                }
                            }
                        } else if (json.exlusionerror) {
                            json.message = '$alertMsg'
                            alert(json.message);
                            return false;
                        }
                      
                        if (type == 'save') {
                            editForm.submit();
                        } else {
                            editForm.submit($('edit_form').action + 'back/edit/');
                        }
                    });
                } else {
                    if (type == 'save') {
                        editForm.submit();
                    } else {
                        editForm.submit($('edit_form').action + 'back/edit/');
                    }
                }
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
