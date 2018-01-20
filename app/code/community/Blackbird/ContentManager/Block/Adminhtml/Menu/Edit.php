<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Menu_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'menu_id';
        $this->_blockGroup = 'contentmanager';
        $this->_controller = 'adminhtml_menu';
        
        parent::__construct();
 
        $this->_updateButton('save', 'label', Mage::helper('contentmanager')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('contentmanager')->__('Delete'));
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
 
    public function getHeaderText()
    {
        if( Mage::registry('menu_data') && Mage::registry('menu_data')->getId() ) {
            return Mage::helper('contentmanager')->__("Edit Menu '%s'", $this->htmlEscape(Mage::registry('menu_data')->getTitle()));
        } else {
            return Mage::helper('contentmanager')->__('Add Menu');
        }
    }
}