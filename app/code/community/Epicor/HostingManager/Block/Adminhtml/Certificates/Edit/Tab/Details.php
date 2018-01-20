<?php

/**
 * Certificates details edit block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('hostingmanager')->__('Certificate Details')));
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Name'),
            'tabindex' => 1,
            'required' => true,
            'name' => 'name'
        ));

        $fieldset->addField('request', 'textarea', array(
            'label' => Mage::helper('hostingmanager')->__('Certificate Signing Request'),
            'required' => false,
            'name' => 'request'
        ));

        $fieldset->addField('private_key', 'textarea', array(
            'label' => Mage::helper('hostingmanager')->__('Private Key'),
            'required' => false,
            'name' => 'private_key'
        ));

        $fieldset->addField('certificate', 'textarea', array(
            'label' => Mage::helper('hostingmanager')->__('Certificate'),
            'required' => false,
            'name' => 'certificate'
        ));

        $fieldset->addField('c_a_certificate', 'textarea', array(
            'label' => Mage::helper('hostingmanager')->__('CA Certificate'),
            'required' => false,
            'name' => 'c_a_certificate'
        ));

        $site = Mage::registry('current_certificate');
        $form->setValues($site->getData());

        return parent::_prepareForm();
    }

}
