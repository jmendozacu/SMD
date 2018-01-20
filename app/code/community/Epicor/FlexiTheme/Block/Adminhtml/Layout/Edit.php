<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_layout';
        $this->_blockGroup = 'flexitheme';
        $this->_mode = 'edit';

        $this->_removebutton('save');
        
        $this->_addButton('save', array(
            'label' => Mage::helper('adminhtml')->__('Save'),
            'onclick' => 'saveEdit()',
            'class' => 'save',
                ), -100);
        
        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveEdit(){
                url = $('edit_form').readAttribute('action');
                submitLayoutForm(url); 
            }

            function saveAndContinueEdit(){
                url = $('edit_form').readAttribute('action')+'back/edit/';
                submitLayoutForm(url); 
            }
            
            function submitLayoutForm(url){
                if(editForm.validate()) {
                    var form_data = $('edit_form').serialize(true);

                    this.ajaxRequest = new Ajax.Request(url, {
                        method: 'post',
                        parameters: form_data,
                        onComplete: function(request) {
                            this.ajaxRequest = false;
                        }.bind(this),
                        onSuccess: function(data) {

                            var json = data.responseText.evalJSON();
                            if (json.type == 'success') {
                                if (json.redirect) {
                                    window.location.replace(json.redirect);
                                }
                            } else {
                                $('loading-mask').hide();
                                if (json.message) {
                                    showMessage(json.message, json.type);
                                }
                            }
                        }.bind(this),
                        onFailure: function(request) {
                            alert('Error occured in Ajax Call');
                        }.bind(this),
                        onException: function(request, e) {
                            alert(e);
                        }.bind(this)
                    });
                }
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('old_layout_data') && Mage::registry('old_layout_data')->getId()) {
            $title = Mage::registry('old_layout_data')->getLayoutName();
            return Mage::helper('adminhtml')->__('Edit Layout "%s"', $this->htmlEscape($title));
        } else {
            return Mage::helper('adminhtml')->__('New Layout');
        }
    }

}
