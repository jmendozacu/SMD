<?php
 
class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $quote = Mage::registry('quote');

        $form->addField('entity_id', 'hidden', array(
            'name' => 'quote_id',
        ));
        if ($quote->getId()) {
            $form->setValues($quote->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}