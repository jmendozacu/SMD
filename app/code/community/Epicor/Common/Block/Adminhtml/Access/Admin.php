<?php

class Epicor_Common_Block_Adminhtml_Access_Admin extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_controller = 'adminhtml_access';
        $this->_blockGroup = 'epicor_common';
        $this->_mode = 'admin';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_removeButton('save');
        $this->_removeButton('back');

        $ajaxUrl = $this->getUrl('adminhtml/epicorcommon_access_admin/updateelements');

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
            
            adminForm = new varienForm('admin_form', '');

            function saveAndContinueEdit(){
                adminForm.submit($('admin_form').action+'back/admin/');
            }
        ";
    }

    public function getHeaderText() {
        return Mage::helper('adminhtml')->__('Access Management Administration');
    }

}
