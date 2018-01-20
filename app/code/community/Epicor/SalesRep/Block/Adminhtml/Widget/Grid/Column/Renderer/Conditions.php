<?php

/**
 * Sales Rep Pricing Rule Conditions Renderer
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Adminhtml_Widget_Grid_Column_Renderer_Conditions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_SalesRep_Model_Pricing_Rule */
        return ($row->getConditions()) ? '<div class="conditions_html" id="conditions_' . $row->getId() . '">' . $row->getConditions()->setJsFormObject('rule_conditions_fieldset')->asHtmlRecursive() . '</div>' : '';
    }

}
