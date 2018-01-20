<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Type
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Renderer_Message extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $messageType = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        return $helper->getMessageType($messageType);
    }

}
