<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Adminhtml_DeleteRequest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'amgdpr';
        $this->_controller = 'adminhtml_deleteRequest';
        $this->_headerText = $this->__('Delete Requests');

        parent::__construct();

        $this->_removeButton('add');
    }
}