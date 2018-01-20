<?php

/**
 * Epicor_Common_Block_Adminhtml_Advanced_Cleardata
 * 
 * Form Container for Clear Data Form
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Cleardata_Clear extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_advanced_cleardata';
        $this->_blockGroup = 'epicor_common';
        $this->_headerText = Mage::helper('epicor_common')->__('Clear Data');
        $this->_mode = 'clear';

        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('save');

        $this->_addButton('clear', array(
            'label' => Mage::helper('epicor_common')->__('Clear selected data types'),
            'onclick' => 'clearFormSubmit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            clearForm = new varienForm('clear_form', '');

            function clearFormSubmit(){
            
                if($$('#clear_form input[type=\'checkbox\']:checked').length > 0) {
                    if(confirm('Are you sure you wish to clear the selected data types? \\nThis action cannot be undone')) {
                        clearForm.submit();
                    }
                } else {
                    alert('Please select one or more data types');
                }
            }
        ";
    }

}
