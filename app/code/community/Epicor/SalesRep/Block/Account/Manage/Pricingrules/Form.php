<?php

/**
 * Sales Rep Account Pricing Rules List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Pricingrules_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        $editable = $helper->canEdit();
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('pricing_rule_form', array('legend' => $this->__('Pricing Rule')));

        $fieldset->addField('pricing_rule_post_url', 'hidden', array(
            'name' => 'pricingRulePostUrl',
            'value' => $this->getUrl('*/*/pricingrulespost')
        ));

        $fieldset->addField('id', 'hidden', array(
            'name' => 'id',
        ));

        $fieldset->addField('delete_message', 'hidden', array(
            'name' => 'deleteMessage',
            'value' => $this->__('Are you sure you want to delete this Pricing Rule?')
        ));

        $fieldset->addField('name', 'text', array(
            'label' => $this->__('Price Rule Name'),
            //'required' => true,
            'name' => 'name',
            'disabled' => !$editable
        ));

        $fieldset->addField('from_date', 'date', array(
            'label' => $this->__('From Date'),
            'required' => false,
            'name' => 'from_date',
            'comment' => 'Change Date Using Date Picker',
            'class' => 'validate-date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'yyyy-MM-dd',
            'value' => Mage::helper('epicor_comm')->getLocalDate(strtotime('-1 week'), 'yyyy-MM-dd'),
            'disabled' => !$editable
        ));

        $fieldset->addField('to_date', 'date', array(
            'label' => $this->__('To Date'),
            'required' => false,
            'name' => 'to_date',
            'comment' => 'Change Date Using Date Picker',
            'class' => 'validate-date',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => 'yyyy-MM-dd',
            'value' => Mage::helper('epicor_comm')->getLocalDate(strtotime('-1 week'), 'yyyy-MM-dd'),
            'disabled' => !$editable
        ));

        $fieldset->addField('priority', 'text', array(
            'label' => $this->__('Priority'),
            //'required' => true,
            'name' => 'priority',
            'class' => 'validate-number',
            'after_element_html' => $this->__('The Higher the Number, the Higher the Priority'),
            'disabled' => !$editable
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => $this->__('Status'),
            //'required' => true,
            'name' => 'is_active',
            'values' => array(
                '1' => $this->__('Active'),
                '0' => $this->__('Inactive')
            ),
            'disabled' => !$editable
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
                    'legend' => Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
                )->setRenderer($renderer);

        $fieldset->addField('rule_conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('catalogrule')->__('Conditions'),
            'title' => Mage::helper('catalogrule')->__('Conditions'),
            'disabled' => !$editable
                //'required' => true,
        ))->setRule(Mage::getModel('catalogrule/rule'))->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $fieldset = $form->addFieldset('calculation', array(
            'legend' => Mage::helper('catalogrule')->__('Pricing Calculation')));

        $fieldset->addType('heading', 'Epicor_Common_Lib_Varien_Data_Form_Element_Heading');

        $fieldset->addField('pricing', 'heading', array(
            'label' => $this->__('Sales Reps can Apply Prices Using the Following Information'),
        ));

        $fieldset->addField('action_operator', 'select', array(
            'label' => $this->__('Apply'),
            //'required' => true,
            'name' => 'action_operator',
            'values' => array(
                'cost' => $this->__('Up to a Percentage above the Cost Price'),
                'list' => $this->__('Down to a Percentage below the Customer Specific Price'),
                'base' => $this->__('Down to a Percentage below the Base Price')
            ),
            'disabled' => !$editable
        ));

        $fieldset->addField('action_amount', 'text', array(
            'label' => $this->__('Margin Amount'),
            //'required' => true,
            'name' => 'action_amount',
            'class' => 'validate-number',
            'disabled' => !$editable
        ));

        if ($editable) {
            $fieldset->addField('updatePricingRuleSubmit', 'submit', array(
                'value' => $this->__('Update'),
                'onclick' => "return pricingRules.rowUpdate();",
                'name' => 'updatePricingRuleSubmit',
                'class' => 'form-button',
            ));

            $fieldset->addField('addPricingRuleSubmit', 'submit', array(
                'value' => $this->__('Add'),
                'onclick' => "return pricingRules.rowUpdate();",
                'name' => 'addPricingRuleSubmit',
                'class' => 'form-button',
            ));
        }

        $this->setForm($form);
    }

}
