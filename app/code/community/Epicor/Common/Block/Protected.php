<?php

/**
 * Protected block, used for displaying blocks that need to be protected by access rights
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Protected extends Mage_Core_Block_Template {

    private $_protection = array();

    public function addProtection($name, $rights) {
        $this->_protection[$name] = $rights;
    }

    protected function _toHtml() {
        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $html = '';
        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getLayout()->getBlock($name);

            if (!$block) {
                Mage::throwException(Mage::helper('core')->__('Invalid block: %s', $name));
            }

            $blockAllowed = true;

            if (isset($this->_protection[$name])) {
                $blockAllowed = $helper->customerHasAccess($this->_protection[$name]['module'], $this->_protection[$name]['controller'], $this->_protection[$name]['action'], $this->_protection[$name]['block'], $this->_protection[$name]['action_type']);
            }
            if ($blockAllowed) {
                $html .= $block->toHtml();
            }
        }

        return $html;
    }

}
