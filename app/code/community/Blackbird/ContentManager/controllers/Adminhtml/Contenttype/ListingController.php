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
class Blackbird_ContentManager_Adminhtml_Contenttype_ListingController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('contentmanager/manage_listing');
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction() {

        $this->loadLayout();
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_contentlist'));
        $this->renderLayout();
    }

    /**
     * Create new content list
     */
    public function newAction() {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit content list
     */
    public function editAction() {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('cl_id');
        $model = Mage::getModel('contentmanager/contentlist');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('This list no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }


        // 4. Register model to use later in listing
        Mage::register('contentlist_data', $model);

        // 5. Build edit form
        $this->loadLayout()
                ->_setActiveMenu('contentmanager/contentlist');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_contentlist_edit'))
                ->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_contentlist_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            try {
                
                $id = 0;
                if (isset($data['cl_id'])) {
                    $id = $data['cl_id'];
                }
                
                //SAVE CONTENT TYPE - init model and set data
                $contentListModel = $this->_saveContentList($data);
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contentmanager')->__('The list has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setContentListData(false);
                
                //SAVE LAYOUT
                $this->_saveLayoutGroups($data, $contentListModel);
                $this->_saveLayoutFields($data, $contentListModel);
                $this->_saveLayoutBlocks($data, $contentListModel);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('cl_id' => $contentListModel->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setContentListData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('cl_id' => $this->getRequest()->getParam('cl_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('cl_id')) {
            try {
                // init model and delete
                $model = Mage::getModel('contentmanager/contentlist');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('contentmanager')->__('The content list has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('cl_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('Unable to find a content list to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_listing');
    }

    /**
     * Save content list and his options from the current CL form in DB
     * @param type $data
     */
    private function _saveContentList($data) {
        //load model or creat a new one
        $contentListModel = Mage::getModel('contentmanager/contentlist');
        
        if (isset($data['cl_id']) && $id = $data['cl_id']) {
            $contentListModel->load($id);
        }

        //update basic data
        $contentListModel->setData($data);

        //save breacrumbs middle
        if (isset($data['breadcrumb_prev_name']))
            $contentListModel->setData('breadcrumb_prev_name', serialize($data['breadcrumb_prev_name']));
        else
            $contentListModel->setData('breadcrumb_prev_name', '');

        if (isset($data['breadcrumb_prev_name']))
            $contentListModel->setData('breadcrumb_prev_link', serialize($data['breadcrumb_prev_link']));
        else
            $contentListModel->setData('breadcrumb_prev_link', '');

        //update date for this model
        if (isset($id) && $id) {
            $contentListModel->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        } else {
            $contentListModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        }
        
        //save the entire model
        $contentListModel->save();

        return $contentListModel;
    }

    /**
     * Save layout fields items
     * @param type $data
     */
    private function _saveLayoutFields($data, $contentList) {
        //delete existing fields
        $collection = Mage::getModel('contentmanager/contentlist_layout_field')->getCollection()->addFieldToFilter('cl_id', $contentList->getId());
        foreach ($collection as $layoutField) {
            $layoutField->delete();
        }

        //save fields
        if (isset($data['layout_field'])) {
            foreach ($data['layout_field'] as $key => $layout_field) {
                $layoutField = Mage::getModel('contentmanager/contentlist_layout_field');
                $layoutField->setColumn($layout_field['column']);
                $layoutField->setSortOrder($layout_field['sort_order']);
                $layoutField->setLabel($layout_field['label']);
                $layoutField->setHtmlClass($layout_field['html_class']);
                $layoutField->setHtmlId($layout_field['html_id']);
                $layoutField->setHtmlTag($layout_field['html_tag']);
                $layoutField->setHtmlLabelTag($layout_field['html_label_tag']);
                if ($layout_field['option_id'] > 0)
                    $layoutField->setOptionId($layout_field['option_id']);
                $layoutField->setClId($contentList->getId());
                if (isset($this->_tmpLocalLayoutGroup[$layout_field['layout_group_id']]))
                    $layoutField->setLayoutGroupId($this->_tmpLocalLayoutGroup[$layout_field['layout_group_id']]);

                $layoutField->setFormat(
                        serialize(array(
                    'type' => isset($layout_field['format']) ? $layout_field['format'] : '',
                    'extra' => isset($layout_field['format_extra']) ? $layout_field['format_extra'] : '',
                    'height' => isset($layout_field['format_height']) ? $layout_field['format_height'] : '',
                    'width' => isset($layout_field['format_width']) ? $layout_field['format_width'] : '',
                    'link' => isset($layout_field['link']) ? $layout_field['link'] : '',
                        ))
                );

                $layoutField->save();
            }
        }
    }

    /**
     * Save layout blocks items
     * @param type $data
     */
    private function _saveLayoutBlocks($data, $contentList) {
        //delete existing blocks
        $collection = Mage::getModel('contentmanager/contentlist_layout_block')->getCollection()->addFieldToFilter('cl_id', $contentList->getId());
        foreach ($collection as $layoutBlock) {
            $layoutBlock->delete();
        }

        //save blocks
        if (isset($data['layout_block'])) {
            foreach ($data['layout_block'] as $key => $layout_block) {
                $layoutBlock = Mage::getModel('contentmanager/contentlist_layout_block');
                $layoutBlock->setColumn($layout_block['column']);
                $layoutBlock->setSortOrder($layout_block['sort_order']);
                $layoutBlock->setLabel($layout_block['label']);
                $layoutBlock->setHtmlClass($layout_block['html_class']);
                $layoutBlock->setHtmlId($layout_block['html_id']);
                $layoutBlock->setHtmlTag($layout_block['html_tag']);
                $layoutBlock->setHtmlLabelTag($layout_block['html_label_tag']);
                if ($layout_block['block_id'] > 0)
                    $layoutBlock->setBlockId($layout_block['block_id']);
                $layoutBlock->setClId($contentList->getId());
                if (isset($this->_tmpLocalLayoutGroup[$layout_block['layout_group_id']]))
                    $layoutBlock->setLayoutGroupId($this->_tmpLocalLayoutGroup[$layout_block['layout_group_id']]);

                $layoutBlock->save();
            }
        }
    }

    /**
     * Save layout groups items
     * @param type $data
     */
    private function _saveLayoutGroups($data, $contentList) {
        //delete existing groups
        $collection = Mage::getModel('contentmanager/contentlist_layout_group')->getCollection()->addFieldToFilter('cl_id', $contentList->getId());
        foreach ($collection as $layoutGroup) {
            $layoutGroup->delete();
        }

        //tmp group to save 2 times to link them to their parent group (if existing only)
        $_layoutGroupArray = array();

        //save groups
        if (isset($data['layout_group'])) {
            foreach ($data['layout_group'] as $key => $layout_group) {
                $layoutGroup = Mage::getModel('contentmanager/contentlist_layout_group');
                $layoutGroup->setColumn($layout_group['column']);
                $layoutGroup->setSortOrder($layout_group['sort_order']);
                $layoutGroup->setLabel($layout_group['label']);
                $layoutGroup->setHtmlName($layout_group['html_name']);
                $layoutGroup->setHtmlClass($layout_group['html_class']);
                $layoutGroup->setHtmlId($layout_group['html_id']);
                $layoutGroup->setHtmlTag($layout_group['html_tag']);
                $layoutGroup->setHtmlLabelTag($layout_group['html_label_tag']);
                $layoutGroup->setLocalParentLayoutGroupId($layout_group['parent_layout_group_id']);
                $layoutGroup->setClId($contentList->getId());

                $layoutGroup->save();

                if ($layoutGroup->getLocalParentLayoutGroupId()) {
                    $_layoutGroupArray[] = $layoutGroup;
                }

                //save correspondance between local group id and final group id
                $this->_tmpLocalLayoutGroup[$layout_group['layout_group_id']] = $layoutGroup->getId();
            }
        }

        //Saved 2 times to link them to their parent group (if existing only)
        foreach ($_layoutGroupArray as $layoutGroup) {
            $layoutGroup->setParentLayoutGroupId($this->_tmpLocalLayoutGroup[$layoutGroup->getLocalParentLayoutGroupId()]);
            $layoutGroup->save();
        }
    }

}
