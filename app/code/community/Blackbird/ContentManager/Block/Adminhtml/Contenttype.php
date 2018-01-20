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

class Blackbird_ContentManager_Block_Adminhtml_Contenttype extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
        $this->_controller = 'adminhtml_contenttype';
        $this->_blockGroup = 'contentmanager';
        $this->_headerText = Mage::helper('contentmanager')->__('Content Types Manager');
        $this->_addButtonLabel = Mage::helper('contentmanager')->__('Add Content Type');
        
        $this->_addButton('import', array(
            'label'     => Mage::helper('contentmanager')->__('Import Content Type'),
            'onclick'   => 'setLocation(\'' . $this->getImportUrl() .'\')',
            'class'     => 'add-variable',
        ));        
        
        parent::__construct();
    }

    public function getImportUrl()
    {
        return $this->getUrl('*/*/import');
    }
}