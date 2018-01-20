<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $layout_block = Mage::registry('layout_data');


        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        if ($layout_block->getId()) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'layout_id',
            ));
            $form->setValues($layout_block->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
