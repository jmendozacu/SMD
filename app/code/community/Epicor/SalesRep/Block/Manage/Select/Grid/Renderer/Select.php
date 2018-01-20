<?php
/**
 * Column Renderer for Branchpickup Select Grid
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Select extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{
    
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }
        if ($this->getColumn()->getLinks() == true) {
            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */
            $masquerade = $customerSession->getMasqueradeAccountId();
            $html = '';
            $rowId = $row->getId();
            $helper = Mage::helper('epicor_salesrep');
            /* @var $helper Epicor_SalesRep_Helper_Data */
            $isSecure = $helper->isSecure();

            $url = Mage::getUrl('salesrep/account/index',  array('_forced_secure' => $isSecure ));
            $redirectUrl = Mage::getUrl('salesrep/account/index',  array('_forced_secure' => $isSecure ));
            $returnUrl = Mage::helper('epicor_comm')->urlEncode($url);   
            $ajax_url = Mage::getUrl('comm/masquerade/masquerade',  array('_forced_secure' => $isSecure ));      
            if ((!empty($masquerade)) && ($rowId == $masquerade))  {
                $html .= $this->__('Currently Selected');
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if (($html != '') && ($action['caption'] != 'Begin Masquerade')) {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ?: ' | ') . '</span>';
                            $html .= $this->_toLinkHtml($action, $row);
                        }
                    }
                }
            } else {
                foreach ($actions as $action) {
                    if (is_array($action)) {
                            $actionId= $action['id'];
                            if($actionId =="return") {
                                //$action['field'] = 'masquerade_as/'.$rowId.'/return_url/'.base64_encode($returnUrl);
                            }                        
                        $html .= $this->_toLinkHtml($action, $row);
                    }
                }
                
            }
            $html .= '<input type="hidden" name="return_url" id="return_url" value="' . $returnUrl . '">';
            $html .= '<input type="hidden" name="jreturn_url" id="jreturn_url" value="' . $redirectUrl . '">';
            $html .= '<input type="hidden" name="ajax_url" id="ajax_url" value="' . $ajax_url . '">';
            return $html;
        } else {
            return parent::render($row);
        }
    }
 
}