<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_DeleteRequest_DenyTemplate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Exception
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/denyPost'),
            'method' => 'post'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $detailsFieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Email Details')
            )
        );

        $detailsFieldset->addField('requests', 'hidden', array (
            'name' => 'requests',
            'value' => implode(',', $this->getRequest()->getParam('ids'))
        ));

        $detailsFieldset->addField(
            'comment',
            'textarea',
            array (
                'label' => $this->__('Comment'),
                'name' => 'comment',
                'required' => true,
            )
        );

        return parent::_prepareForm();
    }
}