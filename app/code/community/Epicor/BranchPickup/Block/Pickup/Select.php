<?php
/**
 * Branchpickup page select page grid
 *
 * @category   Epicor
 * @package    Epicor_BranchPickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_Block_Pickup_Select extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'epicor_branchpickup';
        $this->_controller = 'pickup_select';
        $this->_headerText = Mage::helper('epicor_branchpickup')->__('Select Branch');
        parent::__construct();
        $this->_removeButton('add');
    }

}