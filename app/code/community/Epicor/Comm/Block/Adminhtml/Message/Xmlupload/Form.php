<?php
class Epicor_Comm_Block_Adminhtml_Message_Xmlupload_Form extends Mage_Adminhtml_Block_Widget_Form
{
    const XML_FILE_UPLOAD  = 1;
    const XML_TEXT_UPLOAD  = 2;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/upload'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'layout_block_form', 
            array(
                'legend' => Mage::helper('epicor_comm')->__('XML Upload')
            )
        );
        
        $fieldset->addField(
            'input_type', 
            'select', array(
                'label'  => $this->__('Text or File?'),
                'name'   => 'input_type',
                'required' => true,
                'values' => Mage::getModel('epicor_comm/config_source_yesnoxmlupload')->toOptionArray()
            )
        );
        
        $this
            ->setChild('form_after',$this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('input_type', 'input_type')
            ->addFieldMap('xml_file', 'xml_file')
            ->addFieldMap('xml_message', 'xml_message')
            ->addFieldDependence('xml_file', 'input_type', self::XML_FILE_UPLOAD) // 1 = 'Xml File'
            ->addFieldDependence('xml_message', 'input_type', self::XML_TEXT_UPLOAD) // 2 = 'Xml Text'
        );

        $fieldset->addField(
            'xml_file',
            'file', array(
                'label' => Mage::helper('epicor_comm')->__('XML Message File'),
                'required' => true,
                'name' => 'xml_file'
            )
        );
        
        $fieldset->addField(
            'xml_message', 
            'textarea', array(
                'label' => Mage::helper('epicor_comm')->__('XML Message'),
                'required' => true,
                'name' => 'xml_message'
            )
        );
        
        $form->setValues(Mage::registry('posted_xml_data'));
        return parent::_prepareForm();
    }

}
