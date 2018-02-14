<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
require_once Mage::getModuleDir('controllers', 'Amasty_Commonrules').DS.'Adminhtml'.DS.'Amcommonrules'.DS.'RuleController.php';

class Amasty_Shiprules_Adminhtml_Amshiprules_RuleController extends Amasty_Commonrules_Adminhtml_Amcommonrules_RuleController
{
    protected function _construct()
    {
        parent::_construct();
        $this->_title       = 'Shipping Rule';
        $this->_namespace   = 'amshiprules';
    }
    
    public function newActionHtmlAction()
    {
        $this->_newConditions('actions');
    }
}