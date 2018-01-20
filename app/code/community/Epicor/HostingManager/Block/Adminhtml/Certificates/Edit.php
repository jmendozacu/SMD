<?php

/**
 * Certificates edit form container block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_certificates';
        $this->_blockGroup = 'hostingmanager';
        $this->_mode = 'edit';

        $this->_addButton('generate_csr', array(
            'label' => Mage::helper('hostingmanager')->__('Generate CSR'),
            'onclick' => 'generateCsr()',
            'class' => 'save',
                ), -100);
        
        $this->_addButton('selfSign()', array(
            'label' => Mage::helper('hostingmanager')->__('Create Self Signed Certificate'),
            'onclick' => 'createSsc()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function generateCsr(){
                $('certificate_tabs_csr_content').select('.form-list input').invoke('addClassName','required-entry');
                editForm.submit($('edit_form').action+'generate_csr/1/');
                $('certificate_tabs_csr_content').select('.form-list input').invoke('removeClassName','required-entry');
            }
            function createSsc(){
                $('request').addClassName('required-entry');
                $('private_key').addClassName('required-entry');
                editForm.submit($('edit_form').action+'create_ssc/1/');
                $('request').removeClassName('required-entry');
                $('private_key').removeClassName('required-entry');
            }
        ";

        parent::__construct();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_certificate')->getId()) {
            return $this->htmlEscape(Mage::registry('current_certificate')->getName());
        } else {
            return Mage::helper('hostingmanager')->__('New Certificate');
        }
    }

}
