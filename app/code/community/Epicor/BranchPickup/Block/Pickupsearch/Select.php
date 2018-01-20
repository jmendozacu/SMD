<?php
/**
 * Branchpickup page search page grid
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_Block_Pickupsearch_Select extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_headerText ='';
    public function __construct()
    {	
        $this->_blockGroup = 'epicor_branchpickup';
        $this->_controller = 'pickupsearch_select';
        $isAjax = Mage::app()->getRequest()->getParam('ajax'); 
        if(!$isAjax) {
           $this->_headerText = Mage::helper('epicor_branchpickup')->__('Select Branch');	
        }
        
        parent::__construct();
        $this->_removeButton('add');
    }

}