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

class Blackbird_ContentManager_Block_Adminhtml_Contenttype_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'ct_id';
        $this->_blockGroup = 'contentmanager';
        $this->_controller = 'adminhtml_contenttype';
        
        parent::__construct();
 
        if($this->getRequest()->getParam('ct_id'))
        {
            $this->_addButton('export', array(
                'label'     => Mage::helper('adminhtml')->__('Export'),
                'onclick'   => 'exportContent(\''.$this->getUrl('*/*/export', array('ct_id' => Mage::registry('contenttype_data')->getId())).'\')',
                'class'     => 'add-variable',
            ), 0);
        }
        
        $this->_updateButton('save', 'label', Mage::helper('contentmanager')->__('Save'));
        if(!$this->getRequest()->getParam('ct_id'))
        {
            $this->_removeButton('save');
        }
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
        if( Mage::registry('contenttype_data') && Mage::registry('contenttype_data')->getId() ) {
            return Mage::helper('contentmanager')->__("Edit Content Type '%s'", $this->htmlEscape(Mage::registry('contenttype_data')->getTitle()));
        } else {
            return Mage::helper('contentmanager')->__('Add Content Type');
        }
    }
}