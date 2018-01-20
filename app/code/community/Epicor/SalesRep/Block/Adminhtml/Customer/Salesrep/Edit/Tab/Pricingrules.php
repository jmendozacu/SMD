<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_PricingRules extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{//Mage_Adminhtml_Block_Widget_Grid{

    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrepGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Pricing Rules';
    }

    public function getTabTitle()
    {
        return 'Pricing Rules';
    }

    public function isHidden()
    {
        return false;
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $layout = Mage::app()->getLayout();

        $html .= $layout->createBlock('epicor_salesrep/adminhtml_customer_salesrep_edit_tab_pricingrules_form')->toHtml();
        $html .= '<script type="text/javascript">
                    pricingRules = new Epicor_SalesRep_Pricing.pricingRules(\'pricing_rule_form\',\'pricing_rules_table\',\'pricing_rules\');
                    Validation.add(\'validate-date\', \'Please enter a valid date (YYYY-MM-DD format).\', function(v) {
                        return Validation.get(\'IsEmpty\').test(v) || /^(\d{4})-(\d{1,2})-(\d{1,2})$/.test(v);
                    })

                </script>';

        $html .= $layout->createBlock('epicor_salesrep/adminhtml_customer_salesrep_edit_tab_pricingrules_grid')->setLayout($layout)->toHtml();

        return $html;
    }

}
