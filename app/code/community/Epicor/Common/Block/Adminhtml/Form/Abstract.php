<?php

class Epicor_Common_Block_Adminhtml_Form_Abstract extends Mage_Adminhtml_Block_Widget_Form {

    protected $idSearch = array(
        '][', ']', '['
    );
    protected $idReplace = array(
        '_', '_', '_'
    );

    /**
     * Builds a form based on the config provided
     * 
     * @param array $config
     * 
     * @return void
     */
    protected function _buildForm($config) {

        $form = $this->getForm();

        foreach ($config as $fieldsetId => $fieldsetInfo) {

            $fieldset = $form->addFieldset($fieldsetId, array('legend' => $fieldsetInfo['label']));
            $fieldset->addType('heading', 'Epicor_Common_Lib_Varien_Data_Form_Element_Heading');
            
            foreach ($fieldsetInfo['fields'] as $fieldId => $field) {

                $field['required'] = isset($field['required']) ? $field['required'] : true;
                $field['name'] = isset($field['name']) ? $field['name'] : $fieldId;
                $field['type'] = isset($field['type']) ? $field['type'] : 'text';

                $fieldId = trim(str_replace($this->idSearch, $this->idReplace, $fieldId), '_');

                if(isset($field['renderer'])) {
                    $fieldset->addField($fieldId, $field['type'], $field)->setRenderer(Mage::app()->getLayout()->createBlock($field['renderer']));
                } else {
                    $fieldset->addField($fieldId, $field['type'], $field);
                }
            }
        }

        $this->setForm($form);
    }

}