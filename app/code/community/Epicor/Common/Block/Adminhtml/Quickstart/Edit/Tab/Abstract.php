<?php

class Epicor_Common_Block_Adminhtml_Quickstart_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /* @var $helper_quickstart Epicor_Common_Helper_Quickstart */
        $helper_quickstart = Mage::helper('epicor_common/quickstart');
        $form = $helper_quickstart->_buildForm($form, $this->getKeysToRender(), $this);

        $this->formExtras($form);
        
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getKeysToRender()
    {
        return array();
    }
    
    protected function formExtras($form)
    {
        return $form;
    }
}

