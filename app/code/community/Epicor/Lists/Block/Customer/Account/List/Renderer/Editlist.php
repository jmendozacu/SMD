<?php

/**
 * List Grid link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Renderer_Editlist extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{
    
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }
        if ($this->getColumn()->getLinks() == true) {
            $model           = Mage::getModel('epicor_lists/list');
            $customerSession = Mage::getSingleton('customer/session')->getCustomer();
            $ownerId         = $row->getData('owner_id');
            $checkMasterErp  = $model->isValidEditForErpAccount($customerSession, $row->getId());
            $checkCustomer   = $model->isValidEditForCustomers($customerSession, $row->getId(),$ownerId);
            $isMasterShopper = $customerSession->getData('ecc_master_shopper');
            if (!$isMasterShopper) {
                $customerId = $customerSession->getData('entity_id');
                if ($customerId == $ownerId) {
                    $edit = true;
                } else {
                    $edit = false;
                }
            } else {
                $edit = true;
            }
            
            $html = '';
            if ((!$checkMasterErp) || (!$checkCustomer) || (!$edit)) {
                
            } else {
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        $html .= '';
                        $actionId = $action['id'];
                        if ($actionId == "edit") {
                            $action['field'] = 'id/' . base64_encode($row->getId());
                        }
                        $html .= $this->_toLinkHtml($action, $row);
                    }
                }
            }
            return $html;
        } else {
            return parent::render($row);
        }
    }
   
}