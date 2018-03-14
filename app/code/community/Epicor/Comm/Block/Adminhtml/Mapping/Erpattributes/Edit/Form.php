<?php

/*
 * Form to insert/edit epicor_comm/erp_mapping_attributes table
 */

class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /*
     * check if data is passed to form, else setup empty form  
     */

    protected function _prepareForm() {
        $attributeObject = $this->getAttributeObject();
        if (Mage::getSingleton('adminhtml/session')->getErpattributesMappingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getErpattributesMappingData();
            Mage::getSingleton('adminhtml/session')->getErpattributesMappingData(null);
        } elseif (Mage::registry('erpattributes_mapping_data')) {
            $data = Mage::registry('erpattributes_mapping_data')->getData();
        } else {
            $data = array();
        }

        $yesno = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $form = new Varien_Data_Form(
                array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
                )
        );

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset(
                'mapping_form', array(
            'legend' => Mage::helper('adminhtml')->__('ERP Attribute Information')
                )
        );

        $fieldset->addField(
                'attribute_code', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Attribute Code'),
            'class' => 'required-entry validate-attribute-code',
            'required' => true,
            'name' => 'attribute_code',
                )
        )->setAfterElementHtml('
            <script type="text/javascript">                       
                Validation.add("validate-attribute-code","No spaces. Maximum length of 30, only letters (a-z), numbers (0-9) or underscore(_), first character should be a letter",function(attribute_code){
                    var regex = /^[a-zA-Z][a-zA-Z0-9_]{0,29}$/;
                    if (regex.exec(attribute_code) == null) { 
                        return false;
                    }
                    return true;
                });
            </script>');
        $inputType = $fieldset->addField(
                'input_type', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Input Type'),
            'required' => true,
            'name' => 'input_type',
            'values' => Mage::helper('epicor_comm')->_getEccattributeTypes(),
                )
        );
        $separator = $fieldset->addField(
                'separator', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Separator'),
            'required' => true,
            'name' => 'separator',
                )
        );
        $configurable = $fieldset->addField(
                'use_for_config', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Use to Create Configurable Product'),
            'name' => 'use_for_config',
            'values' => $yesno,
                )
        );
        $quicksearch = $fieldset->addField(
                'quick_search', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Quick Search'),
            'required' => true,
            'name' => 'quick_search',
            'values' => $yesno,
                )
        );
        $advancedsearch = $fieldset->addField(
                'advanced_search', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Advanced Search'),
            'required' => true,
            'name' => 'advanced_search',
            'values' => $yesno,
                )
        );
        $searchweighting = $fieldset->addField(
                'search_weighting', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Search Weighting'),
            'required' => true,
            'class' => 'validate-number validate-not-negative-number',
            'name' => 'search_weighting',
            'note' => Mage::helper('epicor_comm')->__('Value Must be Greater than or Equal to 0'),         
            'value' => 1,
                )
        );
        $layerednavigation = $fieldset->addField(
                'use_in_layered_navigation', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Use In Layered Navigation'),
            'required' => true,
            'name' => 'use_in_layered_navigation',
            'values' => $this->getFilterableAttributeOptions()
                )
        );
        $searchresults = $fieldset->addField(
                'search_results', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Search Results'),
            'required' => true,
            'name' => 'search_results',
            'values' => $yesno,
                )
        );
        $visibleonproductview = $fieldset->addField(
                'visible_on_product_view', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Visible On Product View'),
            'required' => true,
            'name' => 'visible_on_product_view',
            'values' => $yesno,
                )
        );

        $form->setValues($data);
        $displayIfThisType = array('select', 'text', 'textarea', 'date', 'boolean', 'multiselect', 'price', 'weee');
        $fieldsToDisplay = array('quicksearch', 'advancedsearch','searchweighting', 'layerednavigation', 'searchresults', 'visibleonproductview');
        $childBlock = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap($inputType->getHtmlId(), $inputType->getName())
                        ->addFieldMap($separator->getHtmlId(), $separator->getName())
                        ->addFieldMap($configurable->getHtmlId(), $configurable->getName())
                        ->addFieldMap($quicksearch->getHtmlId(), $quicksearch->getName())
                        ->addFieldMap($advancedsearch->getHtmlId(), $advancedsearch->getName())
                        ->addFieldMap($searchweighting->getHtmlId(), $searchweighting->getName())
                        ->addFieldMap($layerednavigation->getHtmlId(), $layerednavigation->getName())
                        ->addFieldMap($searchresults->getHtmlId(), $searchresults->getName())
                        ->addFieldMap($visibleonproductview->getHtmlId(), $visibleonproductview->getName())
                        ->addFieldDependence($separator->getName(), $inputType->getName(), 'multiselect')
                        ->addFieldDependence($configurable->getName(), $inputType->getName(), 'select');
        
            foreach($fieldsToDisplay as $field){                
                $childBlock->addFieldDependence(${$field}->getName(), $inputType->getName(), $displayIfThisType);
            }
        $this->setChild('form_after', $childBlock);
                
        return parent::_prepareForm();
    }

    /*
     * retrieve options for filterable attribute
     */

    protected function getFilterableAttributeOptions() {
        return array(
            array('value' => '0', 'label' => Mage::helper('catalog')->__('No')),
            array('value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')),
            array('value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')),
        );
    }

}
