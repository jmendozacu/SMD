<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is a Tab Interface Block Wrapper for multiple blocks to be part of a tab
 *
 * @method Epicor_Common_Block_Adminhtml_Widget_Grid_Tab_Wrapper setLabel(string $value)
 * @method Epicor_Common_Block_Adminhtml_Widget_Grid_Tab_Wrapper setTitle(string $value)
 * @method Epicor_Common_Block_Adminhtml_Widget_Grid_Tab_Wrapper setCanShowTab(bool $value)
 * @method Epicor_Common_Block_Adminhtml_Widget_Grid_Tab_Wrapper setIsHidden(bool $value)
 * 
 * @author Paul.Ketelle
 */
class Epicor_Common_Block_Adminhtml_Widget_Tab_Wrapper extends Mage_Core_Block_Text_List implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    public function _construct()
    {
        parent::_construct();
        $this->setLabel('Wrapper Tab');
        $this->setTitle('Wrapper Tab');
        $this->setCanShowTab(true);
        $this->setIsHidden(false);
    }
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getLabel();
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->getCanShowTab();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return $this->getIsHidden();
    }

}
