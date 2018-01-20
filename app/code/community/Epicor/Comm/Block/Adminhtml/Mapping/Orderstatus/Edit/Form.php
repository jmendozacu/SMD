<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Orderstatus_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getOrderstatusMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getOrderstatusMappingData();
            Mage::getSingleton('adminhtml/session')->getOrderstatusMappingData(null);
        }
        elseif (Mage::registry('orderstatus_mapping_data'))
        {
            $data = Mage::registry('orderstatus_mapping_data')->getData();
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
 
        $fieldset->addField('code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'code',
             'note'     => Mage::helper('adminhtml')->__('Order Status Code'),
        ));
 
 
         $statuses = Mage::getResourceModel('sales/order_status_collection')
            ->toOptionArray();
        array_unshift($statuses, array('value' => '', 'label' => ''));

        $states = array_merge(array('' => ''),Mage::getSingleton('sales/order_config')->getStates());
        $fieldset->addField('status', 'select',
            array(
                'name'      => 'status',
                'label'     => Mage::helper('sales')->__('Order Status'),
                'class'     => 'required-entry',
                'values'    => $statuses,
                'required'  => true,
            )
        );

//        $fieldset->addField('state', 'select',
//            array(
//                'name'      => 'state',
//                'label'     => Mage::helper('sales')->__('Order State'),
//                'class'     => 'required-entry',
//                'values'    => $states,
//                'required'  => true,
//            )
//        );
        
        $fieldset->addField('sou_trigger', 'select',
            array(
                'name'      => 'sou_trigger',
                'label'     => Mage::helper('sales')->__('Sou Trigger'),
                'values' => Mage::getModel('epicor_comm/config_source_soutrigger')->toOptionArray(),
            )
        );

        $data = $this->includeStoreIdElement($data);
 
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}