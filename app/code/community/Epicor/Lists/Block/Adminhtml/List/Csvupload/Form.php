<?php

class Epicor_Lists_Block_Adminhtml_List_Csvupload_Form extends Mage_Adminhtml_Block_Widget_Form
{

    const XML_FILE_UPLOAD = 1;
    const XML_TEXT_UPLOAD = 2;

    protected function _prepareForm()
    {   
        $listTypes = Mage::getModel('epicor_lists/list_type')->toOptionArray(true);
        $typeIndex = 'Type Index =';
        foreach ($listTypes as $type) {
            $typeIndex .= ' : '.$type['label'].' ';
        }
        $erpLinkType = 'ERP Account Link Type Index = B2B - B : B2C - C : No Specific Link - N : Chosen ERP - E';
        
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
        
        $fieldset->addField('productimportcsv', 'button', array(
            'value' => $this->__('Download Example CSV File'),
            'onclick' => "return window.location = '" . $this->getUrl('adminhtml/epicorlists_list/createnewlistcsv') . "';",
            'name' => 'productimportcsv',
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
            'text' => Mage::helper('epicor_comm')->__('<br><b>'.$typeIndex.'</b><br><br><b>'.$erpLinkType.'</b>'),
        ));

        return parent::_prepareForm();
    }

}
