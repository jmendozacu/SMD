<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Slider_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{        
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $data = Mage::registry('layout_block_data');

        if ($data->getId()) {

        $form->addField('entity_id', 'hidden', array(
            'name' => 'block_id',
        ));
            $form->setValues($data->getData());
        }
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}