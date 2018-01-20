<?php

class Epicor_Common_Block_Adminhtml_Access_Right_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_access_right';
        $this->_blockGroup = 'epicor_common';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $ajaxUrl = $this->getUrl('adminhtml/epicorcommon_access_right/updateelements');
        $javascript = " new Ajax.Request('$ajaxUrl', {
            method:     'get',
            onSuccess: function(transport){
                    alert('Scanning Complete');
            }
        });";

        $this->_addButton('scancontrollers', array(
            'label' => 'Update Element List',
            'onclick' => $javascript,
            'class' => 'add',
        ));


        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'view_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'view_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('access_right_data') && Mage::registry('access_right_data')->getEntityId()) {
            $title = Mage::registry('access_right_data')->getEntityName();
            //   $title= Mage::app()->getLocale()->getCountryTranslation($title);
            return Mage::helper('adminhtml')->__('Edit Access Right "%s"', $this->htmlEscape($title));
        } else {
            return Mage::helper('adminhtml')->__('New Access Right');
        }
    }

}
