<?php

class Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if ( empty($actions) || !is_array($actions) ) {
            return '&nbsp;';
        }
        
        if($this->getColumn()->getLinks() == true) {
            $html = '';
            foreach ($actions as $action) {
                if ( is_array($action) ) {
                    if (!$this->matchesConditions($action, $row)) {
                        continue;
                    }
                    if($html != '') {
                        $html .= '<span class="action-divider">'.($this->getColumn()->getDivider() ?: ' | ').'</span>';
                    }
                    $html .= $this->_toLinkHtml($action, $row);
                }
            }
            return $html;
        } else {
            return parent::render($row);
        }
    
    }
    
    /**
     * Checks for conditions if set
     * 
     * @param  array $action
     * @param  Varien_Object $row
     * @return bool
     */
    private function matchesConditions($action, Varien_Object $row)
    {
        if (!isset($action['conditions']) || !is_array($action['conditions'])) {
            return true;
        }
        
        foreach ($action['conditions'] as $field => $values) {
            $value  = $row->getData($field);
            
            if (!is_array($values)) {
                $values = array($values);
            }

            if (in_array($value, $values)
                || (in_array('null', $values) && is_null($value))
                || (in_array('empty', $values) && empty($value))) {
                continue;
            }
            
            return false;
        }
        
        
        return true;
    }
 
}