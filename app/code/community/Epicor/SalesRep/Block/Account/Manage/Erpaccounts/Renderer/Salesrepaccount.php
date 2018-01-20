<?php

/**
 * Invoice Reorder link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Erpaccounts_Renderer_Salesrepaccount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Manage */
        
        $currentAccount = $this->getColumn()->getCurrentAccount();
        /* @var $currentAccount Epicor_SalesRep_Model_Account */
        
        $accountsIds = $this->getColumn()->getAccountChildrenIds();
        $accountsIds[] = $currentAccount->getId();
        
        $salesRepAccounts = Mage::getModel('epicor_salesrep/account')->getCollection();
        $salesRepAccounts->join(array('erp' => 'epicor_salesrep/erpaccount'), 'main_table.id = erp.sales_rep_account_id', '');
        $salesRepAccounts->addFieldToFilter('erp.erp_account_id', $row->getEntityId());
        $salesRepAccounts->addFieldToFilter('main_table.id', array('in' => $accountsIds));
        
        $thisAccount = false;
        $accountNames = array();
        foreach($salesRepAccounts as $account){
            if($account->getId() == $currentAccount->getId()){
                $thisAccount = true;
            }else{
                $accountNames[] = $this->__('Child account: %s', $account->getName());
            }
        }
        
        if($thisAccount){
            array_unshift($accountNames, $this->__('This account'));
        }
        
        $html = '';
        if(count($accountNames) == 1){
            $html = array_pop($accountNames);
        }elseif(count($accountNames) > 1){
            $divId = 'salesrepaccounts-'.$row->getId();
            $jsCode = "\$('$divId').style.display=\$('$divId').style.display==''?'none':'';window.event.stopPropagation()||(window.event.cancelBubble=true);";
            //$jsCode = "javascript:if(\$('$divId').visible()){\$('$divId').hide()}else{\$('$divId').show()}";
            $html = $this->__('Multiple accounts');
            $html.= ' <a href="javascript:void(0)" title="' . $this->__('Show/Hide') . '" onclick="' . $jsCode . '">' . $this->__('Show/Hide') . '</a>';
            $html .= '<div id="' . $divId . '" style="display: none">' . implode('<br />', $accountNames) . '</div>';
        }

        return $html;
    }

}
