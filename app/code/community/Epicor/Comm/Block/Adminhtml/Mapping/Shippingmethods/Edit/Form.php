<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Shippingmethods_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData();
            Mage::getSingleton('adminhtml/session')->getShippingmethodsMappingData(null);
        }
        elseif (Mage::registry('shippingmethods_mapping_data'))
        {
            $data = Mage::registry('shippingmethods_mapping_data')->getData();
        }
        else
        {
            $data = array();
        }
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
      
        $fieldset = $form->addFieldset('mapping_form', array(
             'legend' =>Mage::helper('adminhtml')->__('Mapping Information')
        ));
        $rowAttributes ="shipping_method_trakcing";
        $rowId = $this->getRequest()->getParam('id');
        $magentoCurrentUrl = Mage::helper("adminhtml")->getUrl("adminhtml/epicorcomm_mapping_shippingmethods/trackingurltest");
        
        $fieldset->addField('shipping_method_code', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Shipping Method'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'shipping_method',
             'values'     => Mage::getModel('epicor_comm/erp_mapping_shipping')->toOptionArray(),
             'note'       => Mage::helper('adminhtml')->__('Required Shipping Method'),
        ));
        $fieldset->addField('erp_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Code Value'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'erp_code',
        ));
        $fieldset->addField('tracking_url', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Tracking Url'),
             'required'  => false,
             'name'      => 'tracking_url',
             'class'     =>'validate-trackurl',
             'note'      => Mage::helper('adminhtml')->__('e.g. http://yourdomain.com/{{TNUM}} - URL should contain this value {{TNUM}}'),
             'after_element_html' => '<input type="hidden" id="edittrack" name="edittrack" value="1"><input type="hidden" id="popupshipurl" value=\'' .$magentoCurrentUrl. '\'><a href="#"  id="aaa" onclick="shippingmethodtrack.openpopup(\'' . $rowAttributes . '\',\'' . $rowId . '\'); return false;">Test Tracking Url</a>',
        ));       
        
        
        $data = $this->includeStoreIdElement($data);
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}