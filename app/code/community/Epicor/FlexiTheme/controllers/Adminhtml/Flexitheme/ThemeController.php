<?php

class Epicor_FlexiTheme_Adminhtml_Flexitheme_ThemeController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected $_aclId = 'flexitheme/theme/themedesign';

    protected function _initAction()
    {
        $this->_title($this->__('FlexiTheme'))->_title($this->__('Skins'));
        $this->loadLayout()
                ->_setActiveMenu('flexitheme/themedesign')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Skin Design'), Mage::helper('adminhtml')->__('Skin Design'));
        return $this;
    }

    public function indexAction()
    {
           $this->_initAction()
                ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->initTheme($id);
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    private function initTheme($id)
    {
        $model = Mage::getModel('flexitheme/theme');
        if ($id) {
            $model->load($id);
            if ($model->getThemeName()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexitheme')->__('Skin does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('theme_id', $id);
        Mage::register('theme_data', $model);
    }

    public function edittabAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $blockName = $this->getRequest()->getParam('tab', null);
        $this->initTheme($id);
        $this->loadlayout();
        $block = $this->getLayout()->createBlock("flexitheme/adminhtml_theme_edit_tab_$blockName");
        Mage::app()->getResponse()->setBody($block->toHtml());
    }
    
    public function validatenameAction()
    {
        $id = $this->getRequest()->getParam('theme_id', null);
        $this->initTheme($id);
        $theme = Mage::registry('theme_data');
        /* @var $theme Epicor_FlexiTheme_Model_Theme */
        $theme->setThemeName($this->getRequest()->getParam('theme_name', ''));
        $validation = $theme->validate();
        if($validation === true) {
            $response = array(
                'type' => 'success'
            );
        } else {
            $response = array(
                'message' => implode('|',$validation), 
                'type' => 'error'
            );
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    public function ajaxSaveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('flexitheme/theme');

            $id = $this->getRequest()->getParam('theme_id');
            if (!empty($id)) {
                $model->load($id);
            }
            else
                unset($data['theme_id']);

            $model->setData($data);
            $old_theme_name = $model->getOrigData('theme_name');
            try {
                if ($id) {
                    $model->setId($id);
                }

                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Skin'));
                }

                echo $model->getId();
                // Process Page Layouts
                if ($old_theme_name != null && $old_theme_name != $model->getThemeName()) {
                    $old_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . Mage::helper('flexitheme')->safeString($old_theme_name);
                    $new_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . Mage::helper('flexitheme')->safeString($model->getThemeName());

                    $file = new Varien_Io_File();
                    $file->open();
                    $file->mv($old_folder, $new_folder);
                }

                if (isset($_FILES['css'])) {
                    // Process css images
                    foreach ($_FILES['css'] as $form_field_code => $form_field_data) {
                        foreach ($form_field_data as $css_selector => $value) {
                            foreach ($value as $key => $filename) {
                                $css_property = $key;
                                $tmp_filename = $filename;
                                break;
                            }
                            if (!empty($filename)) {
                                $tmp_file = $_FILES['css']['tmp_name'][$form_field_code][$css_selector][$css_property];
                                $css_property_value = Mage::helper('flexitheme/theme')->saveFile($model->getThemeName(), $tmp_file, 'img' . $tmp_filename);

                                $data['css'][$form_field_code][$css_selector][$css_property] = $css_property_value;
                                ksort($data['css'][$form_field_code][$css_selector]);
                            }
                        }
                    }
                }
                if (isset($_FILES['imgs'])) {
                    // Process images
                    foreach ($_FILES['imgs'] as $form_field_code => $form_field_data) {
                        foreach ($form_field_data as $target_filename => $value) {

                            if (!empty($value)) {
                                $tmp_file = $_FILES['imgs']['tmp_name'][$target_filename];
                                $css_property_value = Mage::helper('flexitheme/theme')->saveFile($model->getThemeName(), $tmp_file, $target_filename);

                                $data['imgs'] = $target_filename;
                            }
                        }
                    }
                }
                if (isset($data['delete_img'])) {
                    // Process image deletions
                    foreach ($data['delete_img'] as $image) {
                        $skin_folder = Mage::helper('flexitheme')->safeString($model->getThemeName());
                        $image_src = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . $skin_folder . '/images/' . $image;
                        $file = new Varien_Io_File();
                        $file->open();
                        $file->rm($image_src);
                    }
                }
                if (isset($data['css'])) {
                    // Now save & generate css file
                    Mage::helper('flexitheme/theme')->saveCss($data['css'], $model->getId());
                }
            } catch (Exception $e) {
                echo 'Error';
                Mage::log($e);
            }
        }

        exit;
    }

    public function saveAction()
    {

        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('flexitheme/theme');

            $id = $this->getRequest()->getParam('theme_id');
            if (!empty($id)) {
                $model->load($id);
            }
            else
                unset($data['theme_id']);

            $old_theme_name = $model->getOrigData('theme_name');

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('flexitheme')->__('Error saving Skin'));
                }

                // Process Page Layouts
                if ($old_theme_name != null && $old_theme_name != $model->getThemeName()) {
                    $old_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . Mage::helper('flexitheme')->safeString($old_theme_name);
                    $new_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . Mage::helper('flexitheme')->safeString($model->getThemeName());

                    $file = new Varien_Io_File();
                    $file->open();
                    $file->mv($old_folder, $new_folder);
                }

                if (isset($_FILES['css'])) {
                    // Process css images
                    foreach ($_FILES['css']['name'] as $form_field_code => $form_field_data) {
                        foreach ($form_field_data as $css_selector => $value) {
                            foreach ($value as $key => $filename) {
                                $css_property = $key;
                                $tmp_filename = $filename;
                                break;
                            }

                            if (!empty($tmp_filename)) {

                                $tmp_file = $_FILES['css']['tmp_name'][$form_field_code][$css_selector][$css_property];
                                $css_property_value = Mage::helper('flexitheme/theme')->saveFile($model->getThemeName(), $tmp_file, $tmp_filename);

                                $data['css'][$form_field_code][$css_selector][$css_property] = $css_property_value;
                                ksort($data['css'][$form_field_code][$css_selector]);
                            }
                        }
                    }
                }
                if (isset($_FILES['imgs'])) {
                    // Process images
                    foreach ($_FILES['imgs'] as $form_field_code => $form_field_data) {
                        foreach ($form_field_data as $target_filename => $value) {
                            if (!empty($value)) {
                                $tmp_file = $_FILES['imgs']['tmp_name'][$target_filename];

                                $css_property_value = Mage::helper('flexitheme/theme')->saveFile($model->getThemeName(), $tmp_file, $target_filename);

                                $data['imgs'] = $target_filename;
                            }
                        }
                    }
                }
                if (isset($data['delete_img'])) {

                    // Process image deletions
                    foreach ($data['delete_img'] as $image) {
                        $skin_folder = Mage::helper('flexitheme')->safeString($model->getThemeName());
                        $image_src = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . $skin_folder . '/images/' . $image;

                        $file = new Varien_Io_File();
                        $file->open();
                        $file->rm($image_src);
                    }
                }
                if (isset($data['css'])) {
                    // Now save & generate css file
                    Mage::helper('flexitheme/theme')->saveCss($data['css'], $model->getId());
                }
                // Now save & generate css file
                Mage::helper('flexitheme/theme')->buildCss($model);



                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Skin was successfully saved.'));
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

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('flexitheme/theme')->load($id);
                /* @var $model Epicor_FlexiTheme_Model_Theme */
                
                $theme_name = Mage::helper('flexitheme')->safeString($model->getThemeName());
                $model->delete();

                $theme_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . $theme_name;

                if(!in_array($theme_folder,$model->getReservedNames())) {
                    $file = new Varien_Io_File();
                    $file->open();
                    $file->rmdir($theme_folder, true);
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Skin has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Skin to delete.'));
        $this->_redirect('*/*/');
    }

    public function activateAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                Mage::helper('flexitheme/theme')->setActiveLayout($id);
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Skin has been set to active.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        else
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Skin to activate.'));

        $this->_redirect('*/*/');
        return;
    }
    
    public function importAction()
    {

        if ($this->getRequest()->isPost()) {
            // Get Folder names and the like
            try {
                
                $theme_name = $this->getRequest()->getParam('theme_name');
                
                $model = Mage::getModel('flexitheme/theme');
                $model->setThemeName($theme_name);
                $validation = $model->validate();
                if($validation === true) {
                    $model->save();

                    $var_folder = Mage::getBaseDir('base') . DS . 'var' . DS . 'flexitheme' . DS;
                    $var_file = $var_folder . $_FILES['theme_file']['name'];
                    $tmp_file = $_FILES['theme_file']['tmp_name'];
                    $theme_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . Mage::helper('flexitheme')->safeString($theme_name) . DS;

                    // move uploaded file to tmp location
                    $file = new Varien_Io_File();
                    $file->open();
                    $file->checkAndCreateFolder($var_folder);
                    $file->mkdir($theme_folder . DS . 'images');
                    move_uploaded_file($tmp_file, $var_file);

                    // extract archive to theme folder
                    $archiver = new Mage_Archive();
                    $archiver->unpack($var_file, $theme_folder);


                    //load in theme data
                    $theme_data = unserialize($file->read($theme_folder . 'data.bak'));
                    $file->rm($theme_folder . 'data.bak');



                    Mage::helper('flexitheme/theme')->saveCss($theme_data, $model->getId());
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('Skin was successfully imported.'));
                    $this->_redirect('*/*/');
                } else {
                    throw new Exception(implode('|',$validation));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function exportAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $theme = Mage::getModel('flexitheme/theme')->load($id);
                $theme_name = Mage::helper('flexitheme')->safeString($theme->getThemeName());
                $theme_folder = Mage::getBaseDir('skin') . DS . 'frontend' . DS . 'flexitheme' . DS . $theme_name . '/';
                $destination_folder = Mage::getBaseDir('base') . DS . 'var' . DS . 'flexitheme' . DS;
                $destination_file = $destination_folder . 'theme-' . microtime(true) . '.tgz';

                $theme_data = array();
                $theme_css = Mage::getModel('flexitheme/theme_design')->getCollection()
                        ->addFieldToFilter('theme_id', $theme->getId());

                foreach ($theme_css->getItems() as $css_property) {
                    $theme_data[$css_property->getFormFieldCode()][$css_property->getCssSelector()][$css_property->getCssProperty()] = $css_property->getValue();
                }
                $export_data = serialize($theme_data);

                $file = new Varien_Io_File();
                $file->open();
                $file->write($theme_folder . 'data.bak', $export_data);
                $file->mkdir($destination_folder);


                $archiver = new Mage_Archive();

                $source = $archiver->pack($theme_folder, $destination_file, true);
                $file->rm($theme_folder . 'data.bak');


                $fileName = Mage::helper('flexitheme')->safeString($theme->getThemeName(), ' ') . '-' . date('YmdHis', time()) . '.tgz';

                $this->_prepareDownloadResponse($fileName, array('value' => $source, 'type' => 'filename', 'rm' => true));

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexitheme')->__('The Skin has been deleted.'));

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the Skin to delete.'));
    }

    /**
     * Declare headers and content file in responce for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return Mage_Core_Controller_Varien_Action
     */
    protected function _prepareDownloadResponse(
    $fileName, $content, $contentType = 'application/octet-stream', $contentLength = null)
    {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                $isFile = true;
                $file = $content['value'];
                $contentLength = filesize($file);
            }
        }

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
                ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(array('path' => $ioAdapter->dirname($file)));
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    echo $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }

}