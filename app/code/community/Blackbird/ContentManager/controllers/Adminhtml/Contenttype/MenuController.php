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

class Blackbird_ContentManager_Adminhtml_Contenttype_MenuController extends Mage_Adminhtml_Controller_Action
{
	/**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_MenuController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('contentmanager/manage_menu');
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction() {
    	   	
        $this->loadLayout();     
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_menu'));
        $this->renderLayout();
    }

    /**
     * Create new CMS menu
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS menu
     */
    public function editAction()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('menu_id');
        $model = Mage::getModel('contentmanager/menu');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('This menu no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            else{
                $nodes = $model->getTreeNodes();
            }
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in menus
        Mage::register('menu_data', $model);
        Mage::register('nodes_data', isset($nodes)?$nodes:null);

        // 5. Build edit form
        $this->loadLayout()
                ->_setActiveMenu('contentmanager/items');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_menu_edit'))
             ->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_menu_edit_tabs'));
                 
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $menuId = $this->getRequest()->getParam('menu_id');
            $model = Mage::getModel('contentmanager/menu')->load($menuId);
            if (!$model->getId() && $menuId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('This menu no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                
                //get menu id
                $menuId = $model->getId();
                
                // Save nodes
                if(isset($data['nodes']))
                {
                    $this->_saveNodes(null, $data['nodes'], $menuId);
                }
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contentmanager')->__('The menu has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setMenuData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('menu_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setMenuData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('menu_id' => $this->getRequest()->getParam('menu_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('menu_id')) {
            try {
                // init model and delete
                $model = Mage::getModel('contentmanager/menu');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contentmanager')->__('The menu and his nodes have been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('menu_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('Unable to find a menu to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

	/**
     * Save menu nodes - Recursive function
     * @param array $data
     * @param ContentType_Menu $menu
     */
    private function _saveNodes($parentId = null, $nodes, $menuId, $level = null)
    {
        $level = (is_null($level)) ? 0 : $level+1;
        
        //save nodes
        foreach($nodes as $node)
        {
            $nodeId = (isset($node['node_id'])) ? $node['node_id'] : null;
            $model = Mage::getModel('contentmanager/menu_node')->load($nodeId);
            
            if($node === null && $node['is_deleted'] == 1)
            {
                continue;
            }
            elseif($node['is_deleted'] == 1)
            {
                $model->delete();
                continue;
            }
            
            if (!$model->getId() && $nodeId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('This node no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            
            $model->setData($node);
            $model->setMenuId($menuId);
            $model->setParentId($parentId);
            $model->setLevel($level);
            if(isset($node['children']))
            {
                $count = 0;
                foreach($node['children'] as $child)
                {
                    if($child['is_deleted'] == 0)
                    {
                        $count++;
                    }
                }
                $model->setChildrenCount($count);
            }
            else
            {
                $model->setChildrenCount(0);
            }
            
            //format
            $format = array(
                'firstchild' => isset($node['firstchild'])?$node['firstchild']:'',
                'url' => isset($node['url'])?$node['url']:'',
            );
            $model->setFormat(serialize($format));
            
            // Empty form value get a empty string. We must a null value.
            if($model->getData('node_id') == ""){
                $model->unsetData('node_id');
            }
            if($model->getData('entity_id') == ""){
                $model->unsetData('entity_id');
            }

            try {
                // save the data
                $model->save();
                
                if(isset($node['children']) && !empty($node['children'])){
                    $this->_saveNodes($model->getId(), $node['children'], $menuId, $level);
                }

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setMenuData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('menu_id' => $this->getRequest()->getParam('menu_id')));
                return;
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
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_menu');
    }
}