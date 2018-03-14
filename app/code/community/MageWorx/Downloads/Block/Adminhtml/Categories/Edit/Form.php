<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Categories_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $helper = Mage::helper('mageworx_downloads');
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('general_form_legend', array('legend' => $helper->__('General Information')));

        $fieldset->addField(
            'store_id', 'hidden', array(
            'name' => 'store_id'
            )
        );

        $fieldset->addField(
            'title', 'text', array(
            'label' => $helper->__('Name'),
            'name' => 'title',
            'required' => true
            )
        );

        $fieldset->addField(
            'description', 'textarea', array(
            'label' => $helper->__('Short Description'),
            'required' => false,
            'name' => 'description'
            )
        );

        $fieldset->addField(
            'is_active', 'select', array(
            'label' => $helper->__('Status'),
            'name' => 'is_active',
            'values' => $helper->getStatusArray()
            )
        );

        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getData('downloads_data')) {
            $form->setValues($session->getData('downloads_data'));
            $session->setData('downloads_data');
        } elseif (Mage::registry('downloads_data')) {
            $form->setValues(Mage::registry('downloads_data')->getData());
        }
        return parent::_prepareForm();
    }

}
