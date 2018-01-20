<?php

class Epicor_FlexiTheme_Adminhtml_Flexitheme_Layout_Block_CalloutController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    /**
     *
     * @var Epicor_FlexiTheme_Helper_Layout
     */
    private $_helper;

    protected $_aclId = 'flexitheme/layout_blocks/themelayoutcallouts';

    protected function _initAction() {
        $this->_helper = Mage::helper('flexitheme/layout');
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Layout Callout Blocks'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themelayoutcallouts')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Layout Callout Blocks'), Mage::helper('adminhtml')->__('Layout Callout Blocks'));
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
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Callout does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('layout_block_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction() {
        $this->_helper = Mage::helper('flexitheme/layout');
        if ($data = $this->getRequest()->getPost()) {


            $model = Mage::getModel('flexitheme/layout_block');

            $id = $this->getRequest()->getParam('block_id');
            if ($id) {
                $model->load($id);
            }

            // Process Uploaded Image
            if ($_FILES['image']['error'] === 0) {
                $_helper = Mage::helper('flexitheme/layout');
                /* @var $_helper Epicor_FlexiTheme_Helper_Layout */
                $data['image'] = $_helper->saveFile($_FILES['image']['tmp_name'], $_FILES['image']['name']);
            }
            else
                $data['image'] = $data['image_orig'];

            unset($data['image_orig']);
            //set block model data
            $model->setBlockExtra(serialize($data));
            $model->setBlockName($data['name']);
            $model->setBlockType('flexitheme/frontend_template_callout');
            $model->setBlockFlexiType('callout_block');
            $model->setBlockXmlName('callout.' . $this->_helper->safeString($data['name'], ''));

            /*
              $block_xml = '
              <action method="setType"><value>'.$data['type'].'</value></action>
              <action method="setTitle"><value>'.$data['title'].'</value></action>'; */
            $template = '';

            switch ($data['type']) {
                case 'callout':
                    $template_side = 'page/template/callout/image/side.phtml';
                    $template = 'page/template/callout/image/top.phtml';
                    break;
                case 'featured_product':
                    $template_side = 'page/template/callout/product/side.phtml';
                    $template = 'page/template/callout/blank.phtml';
                    break;
                case 'custom_html':
                    $template = 'page/template/callout/custom/default.phtml';
                    $template_side = $template;
                    break;
            }

            $model->setBlockTemplate($template);
            $model->setBlockTemplateLeft($template_side);
            $model->setBlockTemplateRight($template_side);
            $model->save();
            
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Block'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Block was successfully saved.'));
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
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Block has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Block to delete.'));
        $this->_redirect('*/*/');
    }

}