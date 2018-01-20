<?php

class Epicor_Common_Block_Adminhtml_Mapping_Default_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function includeStoreIdElement($data)
    {
        if(!isset($data['store_id'])){
            $data['store_id'] = (int) $this->getRequest()->getParam('store', 0);
        }
        $this->getForm()->getElement('mapping_form')->addField('store_id', 'hidden', array( 'name' => 'store_id', 'required' => true ));

        return $data;
    }
}