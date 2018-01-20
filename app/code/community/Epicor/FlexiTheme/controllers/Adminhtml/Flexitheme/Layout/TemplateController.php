<?php


class Epicor_FlexiTheme_Adminhtml_Flexitheme_Layout_TemplateController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_aclId = 'flexitheme/themeadmin/themelayouttemplate';
    
    protected function _initAction() {
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Layout Templates'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themelayouttemplate')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Theme Layout Template'), Mage::helper('adminhtml')->__('Theme Layout Templates'));
        return $this;
    }
    
    public function indexAction() {
        
        $this->loadLayout();
        $this->_initAction()
                ->renderLayout();
    }
    
    public function newAction() {
        Mage::getSingleton('adminhtml/session')->unsNewTemplateSections();
        $this->_forward('edit');
    }

    public function editAction() {
        Mage::getSingleton('adminhtml/session')->unsNewTemplateSections();
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('flexitheme/layout_template');
        if ($id) {
            $model->load($id);
            if ($model->getTemplateName()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Template does not exist'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('layout_template_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('flexitheme/layout_template');

            $id = $this->getRequest()->getParam('template_id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();
                
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Tempalte'));
                }
                
                $models = Mage::getSingleton('adminhtml/session')->getNewTemplateSections();
                foreach($models as $section) {
                    if($section->getId() < 0)
                    {
                        $section->setId(null);
                        $section->save();
                    }
                    else
                    {
                        $model = Mage::getModel('flexitheme/layout_template_section')->load($section->getId());
                        if($model->getId())
                        {
                            $model->delete();
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Template was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('flexitheme/layout_template');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Template has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Template to delete.'));
        $this->_redirect('*/*/');
    }
    
    public function deleteSectionAction() {
        $section_id = $this->getRequest()->getParam('id');
        $template_id = null;
        $models = Mage::getSingleton('adminhtml/session')->getNewTemplateSections();
        if($section_id < 0) {
           $template_id = $models[$section_id]->getTemplateId();
           unset($models[$section_id]);
        }
        else
        {
            $model = Mage::getModel('flexitheme/layout_template_section')->load($section_id);
            if($model->getId())
            {
                $template_id = $model->getTemplateId();
                $models[$model->getId()] = $model;
            }
        }

        Mage::getSingleton('adminhtml/session')->setNewTemplateSections($models);
        echo Mage::helper('flexitheme')->showMessageHtml('The Section has been removed from the Template.', 'success');
        echo $this->_getSectionHtml($template_id);
    }
   
   public function addSectionAction() {

        $section_name = $this->getRequest()->getParam('section_name');
        $template_id = $this->getRequest()->getParam('id');
        $models = Mage::getSingleton('adminhtml/session')->getNewTemplateSections();
        
        $model = Mage::getModel('flexitheme/layout_template_section');
        $model->setId((count($models)+1) *-1);
        $model->setTemplateId($template_id);
        $model->setSectionName($section_name);
        
        $models[$model->getId()] = $model;
        
        Mage::getSingleton('adminhtml/session')->setNewTemplateSections($models);
        
        echo Mage::helper('flexitheme')->showMessageHtml('The Section has been added to the Template '.$group.'.', 'success');
        echo $this->_getSectionHtml($template_id);
    }
    
    private function _getSectionHtml($template_id) {
        Mage::getSingleton('adminhtml/session')->setTempTemplateId($template_id);
        
        $block_add_btn = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_template_edit_tab_sections_add');
        $block = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_template_edit_tab_sections');
        $html = $block_add_btn->toHtml().$block->toHtml();
        Mage::getSingleton('adminhtml/session')->unsTempTemplateId();
        return $html;
    }
}