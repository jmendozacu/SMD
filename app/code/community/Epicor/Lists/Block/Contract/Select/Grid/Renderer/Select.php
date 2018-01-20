<?php

/**
 * Column Renderer for Contract Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Select_Grid_Renderer_Select extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        if ($this->getColumn()->getLinks() == true) {

            $contractHelper = Mage::helper('epicor_lists/frontend_contract');
            /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
            $selectedContract = $contractHelper->getSelectedContract();
        
            $html = '';

            if ($row->getId() == $selectedContract) {
                $html .= $this->__('Currently Selected');
                foreach ($actions as $action) {
                    if (is_array($action)) {
                        if (($html != '') && ($action['caption'] !='Select')) {
                            $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ? : ' | ') . '</span>';
                            $html .= $this->_toLinkHtml($action, $row);
                        }
                    }
                }                
            } else {
                foreach ($actions as $action) {
                    if (is_array($action)) {
                    if ($html != '') {
                        $html .= '<span class="action-divider">' . ($this->getColumn()->getDivider() ? : ' | ') . '</span>';
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
