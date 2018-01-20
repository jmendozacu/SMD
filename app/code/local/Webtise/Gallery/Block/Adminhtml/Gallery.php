<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:46
 */

class Webtise_Gallery_Block_Adminhtml_Gallery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Set blockGroup
     * Set controller(not actual controller, but directory for the files that 'control' the grid)
     * Set headerText
     */
    public function _construct(){
        $this->_controller         = 'adminhtml_gallery';
        $this->_blockGroup         = 'gallery';
        parent::_construct();
        $this->_headerText         = Mage::helper('gallery')->__('Gallery');
        $this->_updateButton('add', 'label', Mage::helper('gallery')->__('Add Gallery'));

        $this->setTemplate('gallery/grid.phtml');
    }
}