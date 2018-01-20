<?php

class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $list = Mage::getModel('epicor_lists/list')->load($id);
        /* @var $list Epicor_Lists_Model_List */
        
        if ($list->getType() != 'Pr' && $list->getTypeInstance()->isSectionEditable('products')) {
            $conditions = $list->getConditions();
            $rule = Mage::getModel('epicor_lists/list_rule');
            if ($conditions) {
                $rule->setConditionsSerialized($conditions);                                     // the conditions is looking for this field. no matter what you have saved your field as, populate this value here
                $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
            }
            $form = new Varien_Data_Form();
            $form->setHtmlIdPrefix('rule_');

            $fieldset = $form->addFieldset('conditions_option', array(
                    'legend' => Mage::helper('epicor_lists')->__('Conditions'))
                );
            $fieldset->addField('is_enabled', 'checkbox', array(
                'label'     => Mage::helper('epicor_lists')->__('Link products to list conditionally?'),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
                'name'      => 'rule_is_enabled',
                'checked'    => $conditions ? true : false,
            ));
            $isEnabledJs = $fieldset->addField('is_enabled_js', 'hidden', false);
            $isEnabledJs->setAfterElementHtml('
                    <script> 
                        if($("rule_is_enabled").checked){
                            $("rule_is_enabled").value = 1;
                            $$(".adminhtml-epicorlists-list-edit .rule-tree").each(function(a){
                                a.show();
                            });
                        }else{
                            $("rule_is_enabled").value = 0;
                            $$(".adminhtml-epicorlists-list-edit .rule-tree").each(function(a){
                                a.hide();
                            });                    
                        }
                        $("rule_is_enabled").observe("change", function(){  					  
                            if(this.value == "1"){
                              $$(".rule-tree").first().show();
                            }else{
                              $$(".rule-tree").first().hide();
                            }  
                       });
                    </script>
                    ');
            
            $checked = in_array('E', $list->getSettings()) ? true : false; 
            $fieldset->addField('exclude_selected_products', 'checkbox', array(
                'label'     => Mage::helper('epicor_lists')->__('Exclude selected Products?'),
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
                'name'      => 'exclude_selected_products',
                'checked'    => $checked 
            ));           

            // -------------- add conditions code below ----------------
            $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('epicor/lists/promo/fieldset.phtml')             // this refers to newly created fieldset
                ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

            $fieldset = $form->addFieldset('conditions_fieldset', array(
                    'legend' => Mage::helper('epicor_lists')->__('Conditions (leave blank for all products)'),
                ))->setRenderer($renderer);


            $fieldset->addField('conditions', 'text', array(
                'name' => 'conditions',
                'label' => Mage::helper('catalogrule')->__('Conditions'),
                'title' => Mage::helper('catalogrule')->__('Conditions'),
            ))->setRule($rule)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

            // -------------- conditions code above ----------------

            $data = $rule->getData();
            $data['exclude_selected_products'] = 1;
            $form->setValues($data);
            // $form->setValues($rule->getData());
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

}