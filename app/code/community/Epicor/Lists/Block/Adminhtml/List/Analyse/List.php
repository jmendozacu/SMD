<?php

/**
 * List Admin actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Analyse_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Lists');

        parent::__construct();
    }

}
