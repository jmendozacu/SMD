<?php

/**
 * Certificates generate csr details block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates_Edit_Tab_Csr extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('hostingmanager')->__('Generate CSR')));

        $fieldset->addField('country', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Country'),
            'required' => false,
            'name' => 'country',
            'note'     => Mage::helper('adminhtml')->__('Please enter the 2 character ISO country code<br>eg. GB'),
            'class' => 'validate-length maximum-length-2 minimum-length-2'
            
        ));

        $fieldset->addField('state', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('State'),
            'required' => false,
            'name' => 'state',
            'note'     => Mage::helper('adminhtml')->__('eg. Yorkshire'),
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('City'),
            'required' => false,
            'name' => 'city',
            'note'     => Mage::helper('adminhtml')->__('eg. York'),
        ));

        $fieldset->addField('organisation', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Organisation'),
            'required' => false,
            'name' => 'organisation',
            'note'     => Mage::helper('adminhtml')->__('eg. Epicor'),
        ));

        $fieldset->addField('department', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Department'),
            'required' => false,
            'name' => 'department',
            'note'     => Mage::helper('adminhtml')->__('eg. eCommerce'),
        ));

        $fieldset->addField('domain_name', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Domain Name'),
            'required' => false,
            'name' => 'domain_name',
            'note'     => Mage::helper('adminhtml')->__('eg. www.domain.com'),
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('hostingmanager')->__('Email'),
            'required' => false,
            'class' => 'validate-email',
            'name' => 'email',
            'note'     => Mage::helper('adminhtml')->__('eg. webmaster@example.com'),
        ));
        
        $site = Mage::registry('current_certificate');
        $form->setValues($site->getData());

        return parent::_prepareForm();
    }
    
    public function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        
        $html .= '<p>'.$this->__('Generating a new CSR will create / overwrite the Private Key and Certificate Signing Request.').'</p>';
        
        return $html;
    }

}
