<?php

/**
 * Exclude/Include Delivery Methods Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 * Builds ERP Accounts Delivery methods exclude/include Form
 *
 * @return Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Delivery_Form
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Delivery_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('valid_delivery_form');
    }
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('conditions_option', array('legend' => Mage::helper('epicor_lists')->__('Conditions')));
        
        $erpGroup = Mage::getModel('epicor_comm/customer_erpaccount')->load($this->getRequest()->getParam('id'));

        if(!(is_null($erpGroup->getAllowedDeliveryMethods()) && is_null($erpGroup->getAllowedDeliveryMethodsExclude()))){
            $checked = is_null($erpGroup->getAllowedDeliveryMethods())? true:false;
            $value = is_null($erpGroup->getAllowedDeliveryMethods())? 1:0;
        }else{
            $checked = true;
            $value = 1;
        }
        $fieldset->addField('exclude_selected_delivery', 'checkbox', array(
                'label'     => Mage::helper('epicor_lists')->__('Exclude selected Delivery Methods?'),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
                'name'      => 'exclude_selected_delivery',
                'checked' => $checked,
                'value'   => $value
            ));

        $this->setForm($form);

        return parent::_prepareForm();
    }


}
