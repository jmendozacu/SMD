<?php

/**
 * Epicor_Upgrade_Block_Adminhtml_Advanced_Cleardata
 * 
 * Form Container for Clear Data Form
 * 
 * @category   Epicor
 * @package    Epicor_upgrade
 * @author     Epicor Websales Team
 */
class Epicor_Upgrade_Block_Adminhtml_Advanced_Magealllatest extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_advanced';
        $this->_blockGroup = 'epicor_upgrade';
        $this->_headerText = Mage::helper('epicor_upgrade')->__('Check Mage All Latest File Patch');
        $this->_mode = 'magealllatest';

        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('save');
        $this->_removeButton('reset');

        if (!Mage::registry('mage_all_latest_file_exist')) {
            $this->_addButton('patch', array(
                'label' => Mage::helper('epicor_upgrade')->__('Patch File'),
                'onclick' => 'patchFormSubmit()',
                'class' => 'save',
                    ), -100);


            $this->_formScripts[] = "
                patchForm = new varienForm('patch_form', '');

                function patchFormSubmit(){
                    patchForm.submit();
                }
            ";
        }
    }

}
