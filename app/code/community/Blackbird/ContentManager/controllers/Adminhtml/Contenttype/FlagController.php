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

class Blackbird_ContentManager_Adminhtml_Contenttype_FlagController extends Mage_Adminhtml_Controller_Action
{
    private $_tmpFieldset = array(); //temporary save new fieldsets id to link them to new fields
    
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('contentmanager/manage_flag');
        return $this;
    }
    
    public function indexAction() {
        $this->loadLayout();
        $this->_initAction();

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_flag_edit'))
             ->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_flag_edit_tabs'));

        $this->renderLayout();
    }
   
    public function saveAction()
    {
    	if ($data = $this->getRequest()->getParams()) {
            try {
                    
                //SAVE CONTENT TYPE - init model and set data
                    $this->_saveFlag($data);
                    
                //SUCCESS MESSAGE AND REDIRECT
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Flags were successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFlagData(false);

                $this->_redirect('*/*/');
                return;
                    
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFlagData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit');
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * Save all flags in DB
     * @param type $data
     */
    private function _saveFlag($data)
    {
        foreach($data as $key => $value)
        {
            if(strpos($key, 'store_') !== false)
            {
                //load model or creat a new one
                $flagModel = Mage::getModel('contentmanager/flag')->load(str_replace('store_', '', $key));
                $flagModel->setValue($value);

                //save the model
                $flagModel->save();
            }
        }
    }
    
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_flag');
    }
}