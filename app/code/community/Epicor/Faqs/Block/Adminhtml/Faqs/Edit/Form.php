<?php

/**
 * F.A.Q. adminhtml edit form  
 * 
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 *
 */
class Epicor_Faqs_Block_Adminhtml_Faqs_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Prepare form action
     *
     * @return Epicor_Faqs_Block_Adminhtml_Faqs_Edit_Form
     */
    protected function _prepareForm() {
        /* @var $model Epicor_Faqs_Model_Faqs */
        $model = Mage::helper('epicor_faqs')->getFaqsItemInstance();
        //Create form object
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' =>
                $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('edit_form', array('legend' => 'Information'));

        //Add fields:
        $fieldset->addField('weight', 'text', array(
            'label' => 'Weight',
            'required' => true,
            'name' => 'weight',
            'style' => 'width:50px'
        ));
        $fieldset->addField('question', 'text', array(
            'label' => 'Question',
            'required' => true,
            'name' => 'question'
        ));
        //WYSIWYG Answer field
        $fieldset->addField('answer', 'editor', array(
            'name' => 'answer',
            'label' => Mage::helper('epicor_faqs')->__('Answer'),
            'title' => Mage::helper('epicor_faqs')->__('Answer'),
            'style' => 'height:15em',
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg' => true,
            'required' => true
        ));
        $fieldset->addField('keywords', 'text', array(
            'label' => 'Keywords',
            'required' => false,
            'name' => 'keywords',
            'note' => 'separate keywords with a comma e.g keyword1, keyword2, keyword3'
        ));
        /*
         * Creates an array with all the available stores' id and frontname to populate the checkboxes field
         */
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $stores) {
            $store = Mage::app()->getStore($stores);
            $values[] = array('value' => $store->getId(), 'label' => $store->getName());
        }
        $fieldset->addField('stores', 'checkboxes', array(
            'label' => 'Stores',
            'required' => true,
            'name' => 'stores[]',
            'values' => $values
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        //Dispatch an event 
        Mage::dispatchEvent('adminhtml_faqs_edit_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

}
