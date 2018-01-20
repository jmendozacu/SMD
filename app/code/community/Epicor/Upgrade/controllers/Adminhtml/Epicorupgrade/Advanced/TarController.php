<?php

/**
 * Upgrade Controller
 *
 * @category   Epicor
 * @package    Epicor_Upgrade
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Upgrade_Adminhtml_Epicorupgrade_Advanced_TarController extends Mage_Adminhtml_Controller_Action
{
    
    protected $_aclId = null;
    
    protected function _isAllowed() {
        
        if($this->_aclId){
            return Mage::getSingleton('admin/session')->isAllowed($this->_aclId);
        }else{
            return true;
        }
    }
    
    protected function setAlcId($aclId){
        $this->_aclId = $aclId;
    }

    protected function _initAction()
    {
        $helper = Mage::helper('epicor_upgrade');
        /* @var $helper Epicor_Upgrade_Helper_Data */

        Mage::register('tar_file_patched', $helper->checkTarFile());

        $this->loadLayout()
                ->_setActiveMenu('epicor_upgrade/advanced/tar')
                ->_addBreadcrumb(Mage::helper('epicor_upgrade')->__('Check Tar File Patch'), Mage::helper('epicor_upgrade')->__('Check Tar File Patch'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
                ->renderLayout();
    }

    public function patchAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $helper = Mage::helper('epicor_upgrade');
                /* @var $helper Epicor_Upgrade_Helper_Data */

                $helper->fixTarFile();

                if($helper->checkTarFile()) {
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_upgrade')->__('Tar File was successfully Patched.'));
                } else {
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_upgrade')->__('Could not patch Tar.php, please check file permissions'));
                }
                Mage::getSingleton('core/session')->setFormData(false);

                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('*/*/index');
            }
        }
    }

}
