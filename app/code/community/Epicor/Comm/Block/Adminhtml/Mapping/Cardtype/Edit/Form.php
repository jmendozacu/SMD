<?php

class Epicor_Comm_Block_Adminhtml_Mapping_Cardtype_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{

    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getCardtypeMappingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getCardtypeMappingData();
            Mage::getSingleton('adminhtml/session')->getCardtypeMappingData(null);
        } elseif (Mage::registry('cardtype_mapping_data')) {
            $data = Mage::registry('cardtype_mapping_data')->getData();
        } else {
            $data = array();
        }

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
            'mapping_form',
            array(
            'legend' => Mage::helper('adminhtml')->__('Mapping Information')
            )
        );

        $fieldset->addField(
            'payment_method', 'select',
            array(
            'label' => Mage::helper('adminhtml')->__('Payment Method'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'payment_method',
            'values' => $this->_getPaymentMethods(),
            )
        );

        $fieldset->addField(
            'magento_code', 'text',
            array(
            'label' => Mage::helper('adminhtml')->__('Card Type Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'magento_code',
            )
        );
        $fieldset->addField(
            'erp_code', 'text',
            array(
            'label' => Mage::helper('adminhtml')->__('ERP Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'erp_code',
            )
        );

        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();
    }

    public function _getPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        $methods = array(array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

        $methods['all'] = array(
            'label' => 'All Payment Methods',
            'value' => 'all',
        );
        
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }

}
