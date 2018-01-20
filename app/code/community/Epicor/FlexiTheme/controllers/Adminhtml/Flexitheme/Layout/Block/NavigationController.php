<?php


class Epicor_FlexiTheme_Adminhtml_Flexitheme_Layout_Block_NavigationController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    /**
     *
     * @var Epicor_FlexiTheme_Helper_Layout
     */
    private $_helper;
    
    protected $_aclId = 'flexitheme/layout_blocks/themelayoutnavigation';
    
    protected function _initAction() {
        $this->_helper =  Mage::helper('flexitheme/layout');
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Layout Navigation Blocks'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themelayoutnavigation')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Layout Navigation Blocks'), Mage::helper('adminhtml')->__('Layout Navigation Blocks'));
        return $this;
    }
    
    public function indexAction() {
        
        $this->loadLayout();
        $this->_initAction()
                ->renderLayout();
    }
    
    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('flexitheme/layout_block');
        if ($id) {
            $model->load($id);
            if ($model->getBlockName()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->getBlockExtra(serialize($data));
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Navigation does not exist'));
                $this->_redirect('*/*/');
            }
        }

        Mage::register('layout_block_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
   
   public function addLinkAction() {
        
        $block_id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPost();
        $models = Mage::getSingleton('adminhtml/session')->getNewNavigationLinks();
        
        $model = Mage::getModel('flexitheme/layout_block_link');
        $model->setId((count($models)+1) *-1);
        $model->setDisplayTitle($data['display_title']);
        $model->setTootltipTitle($data['tooltip_title']);
        $model->setCmsPageId($data['cms_page_id']);
        $model->setLinkUrl($data['link_url']);
        $model->setLinkIdentifier($data['link_identifier']);
        
        $models[$model->getId()] = $model;
        
        Mage::getSingleton('adminhtml/session')->setNewNavigationLinks($models);
        
        echo Mage::helper('flexitheme')->showMessageHtml('The Link has been added to the Navigation Block.', 'success');
        echo $this->_getLinksHtml($block_id);
    }
    
    public function deleteLinkAction() {
        $link_id = $this->getRequest()->getParam('id');
        $link_id = $this->getRequest()->getParam('id');
        $models = Mage::getSingleton('adminhtml/session')->getNewNavigationLinks();
        if($link_id < 0) {
           unset($models[$link_id]);
        }
        else
        {
            $model = Mage::getModel('flexitheme/layout_block_link')->load($link_id);
            if($model->getId())
                $models[$model->getId()] = $model;
        }

        Mage::getSingleton('adminhtml/session')->setNewNavigationLinks($models);
        echo Mage::helper('flexitheme')->showMessageHtml('The Section has been removed from the Template.', 'success');
        echo $this->_getLinksHtml($block_id);
    }
    
    public function _getLinksHtml($block_id) {
        Mage::getSingleton('adminhtml/session')->setTempTemplateId($block_id);
        $block_add_btn = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_block_navigation_edit_tab_links_add');
        $block=$this->getLayout()->createBlock('flexitheme/adminhtml_layout_block_navigation_edit_tab_links');
        $html = $block_add_btn->toHtml().$block->toHtml();
        Mage::getSingleton('adminhtml/session')->unsTempTemplateId();
        return $html;
      
    }

    public function saveAction() {
        $this->_helper =  Mage::helper('flexitheme/layout');
        if ($data = $this->getRequest()->getPost()) {
            
            $model = Mage::getModel('flexitheme/layout_block');

            try {
                $id = $this->getRequest()->getParam('block_id');
                if ($id) {
                    $model->load($id);
                }

                //set block model data
                $model->setBlockExtra(serialize($data));
                $model->setBlockName($data['name']);
                $model->setBlockType('flexitheme/frontend_template_navigation');
                $model->setBlockFlexiType('navigation_block');
                $model->setBlockXmlName('navigation.'.$this->_helper->safeString($data['name'], ''));
                $model->setBlockTemplate('page/template/navigation/top.phtml');
                $model->setBlockTemplateLeft('page/template/navigation/side.phtml');
                $model->setBlockTemplateRight('page/template/navigation/side.phtml');
                $model->setBlockTemplateFooter('page/template/navigation/top.phtml');
                $model->save();
                
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Navigation'));
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('flexitheme')->__('Navigation was successfully saved.')
                );
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
                $model = Mage::getModel('flexitheme/layout_block');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Navigation has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Navigation to delete.'));
        $this->_redirect('*/*/');
    }
}