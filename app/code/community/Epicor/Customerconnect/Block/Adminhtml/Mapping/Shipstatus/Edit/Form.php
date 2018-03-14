<?php

/**
 * Block class for Ship status  mapping form
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form {

    protected function _prepareForm() {
        if (Mage::getSingleton('adminhtml/session')->getShipstatusMappingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getShipstatusMappingData();
            Mage::getSingleton('adminhtml/session')->getShipstatusMappingData(null);
        } elseif (Mage::registry('shipstatus_mapping_data')) {
            $data = Mage::registry('shipstatus_mapping_data')->getData();
        } else {
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
            'legend' => Mage::helper('adminhtml')->__('Mapping Information')
        ));
        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('customerconnect')->__('Ship Status Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
            'note' => Mage::helper('adminhtml')->__('Ship Status code'),
        ));

        $fieldset->addField('description', 'text', array(
            'name' => 'description',
            'label' => Mage::helper('adminhtml')->__('Ship Status Description'),
            'class' => 'required-entry',
            'required' => true,
                )
        );

        $fieldset->addField('status_help', 'textarea', array(
            'name' => 'status_help',
            'label' => Mage::helper('adminhtml')->__('Ship Status Help'),
            'class' => 'required-entry',
            'required' => true,
                // 'renderer'=>'customerconnect/adminhtml_mapping_shipstatus_renderer_textarea',
                )
        );
        $is_deafult = isset($data['is_default']) ? $data['is_default'] : 1;
        $fieldset->addField('is_default', 'checkbox', array(
            'label' => Mage::helper('adminhtml')->__('Is Default'),
            'name' => 'is_default',
            'value' => array(0, 1),
            'checked' => ($is_deafult == 1) ? 1 : 0,
            'onclick' => 'this.value = this.checked ? 1 : 0;',
                //'disabled' => false,
                //'readonly' => false,
        ));
        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();
    }

}
