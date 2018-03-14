<?php
/*
 * Form for updating epicor_comm/erp_mapping_attributes by csv 
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Csvupload_Form extends Mage_Adminhtml_Block_Widget_Form
{

    const XML_FILE_UPLOAD = 1;
    const XML_TEXT_UPLOAD = 2;

    /*
     * Setup Form for updating epicor_comm/erp_mapping_attributes by csv 
     */
    protected function _prepareForm()
    {   
        $attributeTypes = Mage::helper('epicor_comm')->_getEccattributeTypes();
        $typeIndex = 'Type Index =';
        array_shift($attributeTypes);
        $types = implode(", ", array_keys($attributeTypes));
        $count = 1;
        $types = str_replace('multiselect', 'multi_selec', $types); // don't want multiselect to change
        $types = str_replace('select', 'select (for Dropdown) ', $types);
        $types = str_replace('multi_selec', 'multiselect', $types);  
        $types = str_replace('weee', 'weee (for Fixed Product Tax) ', $types);
        $typeIndex = "Valid Attribute Types : ".$types; 
        
        $form = new Varien_Data_Form(
            array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/csvupload'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'layout_block_form', array(
            'legend' => Mage::helper('epicor_comm')->__('CSV Upload')
            )
        );
        
        $fieldset->addField('attributeimportcsv', 'button', array(
            'value' => $this->__('Download Example CSV File'),
            'onclick' => "return window.location = '" . $this->getUrl('adminhtml/epicorcomm_mapping_erpattributes/createNewErpattributesCsv') . "';",
            'name' => 'attributeimportcsv',
            'class' => 'form-button'
        ));
        
        $fieldset->addField(
            'csv_file', 'file', array(
            'label' => Mage::helper('epicor_comm')->__('CSV File'),
            'required' => true,
            'name' => 'csv_file'
            )
        );
        
        $fieldset->addField('typeindex', 'note', array(
            'text' => Mage::helper('epicor_comm')->__('<br>'.$typeIndex.'<br>'),
        ));

        return parent::_prepareForm();
    }

}
