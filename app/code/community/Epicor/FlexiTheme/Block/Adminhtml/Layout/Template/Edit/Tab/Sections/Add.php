<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Template_Edit_Tab_Sections_Add extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $template_id = Mage::getSingleton('adminhtml/session')->getTempTemplateId();
        
        if(Mage::registry('layout_template_data'))
            $template_id = Mage::registry('layout_template_data')->getId();
            
        $onClickArgs=array(
          'id'=>$template_id  
        );
        $url = Mage::getUrl('*/*/addsection',$onClickArgs);
        $onclick = "submitAndReloadArea(form_tabs_form_section_content,'$url')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->getButtonHtml('Add', $onclick, 'save');

        //$this->setChild('header', $button);

        $fieldset = $form->addFieldset('add_layout_template_sections', array(
            'legend' => Mage::helper('adminhtml')->__('Add Section'),
            'header_bar' => $button
                ));

        $fieldset->addField('section_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('section Name'),
            'name' => 'section_name'
        ));
        
        return parent::_prepareForm();
    }

}


