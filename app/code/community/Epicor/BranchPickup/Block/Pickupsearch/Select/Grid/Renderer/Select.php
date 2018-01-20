<?php
/**
 * Column Renderer for Branchpickup Select Grid
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_Block_Pickupsearch_Select_Grid_Renderer_Select extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{
    
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }
        
        if ($this->getColumn()->getLinks() == true) {
            
            $branchHelper      = Mage::helper('epicor_branchpickup');
            /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
            $getSelectedBranch = $branchHelper->getSelectedBranch();
            
            $html = '';
            
            if ($row->getCode() == $getSelectedBranch) {
                $html .= $this->__('Currently Selected');
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if (($html != '') && ($action['caption'] != 'Select')) {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ?: ' | ') . '</span>';
                            $html .= $this->_toLinkHtml($action, $row);
                        }
                    }
                }
            } else {
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if ($html != '') {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ?: ' | ') . '</span>';
                        }
                        $html .= $this->_toLinkHtml($action, $row);
                    }
                }
                
            }
            $helperBranchPickup = Mage::helper('epicor_branchpickup/branchpickup');
            /* @var $helper Epicor_BranchPickup_Helper_Branchpickup */
            $url                = Mage::getUrl('branchpickup/pickup/changepickuplocation', $helperBranchPickup->issecure());
            $cartPopupurl                = Mage::getUrl('branchpickup/pickup/cartpopup', $helperBranchPickup->issecure());
            $selectbranch                = Mage::getUrl('branchpickup/pickup/selectbranchajax', $helperBranchPickup->issecure());
            $html .= '<input type="hidden" name="ajaxpickupbranchurl" id="ajaxpickupbranchurl" value="' . $url . '">';
            $html .= '<input type="hidden" name="ajaxcode" id="ajaxcode" value="' . $row->getCode() . '">';
            $html .= '<input type="hidden" name="cartpopupurl" id="cartpopupurl" value="' . $cartPopupurl . '">';
            $html .= '<input type="hidden" name="selectbranch" id="selectbranch" value="' . $selectbranch . '">';
            return $html;
        } else {
            return parent::render($row);
        }
    }
    
}