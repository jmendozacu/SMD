<?php

/**
 * 
 * Access management group info block
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_Details_Info extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct();
        $this->setTitle($this->__('Group Information'));
        $this->setTemplate('epicor_common/access/management/groups/details/info.phtml');
    }

}
