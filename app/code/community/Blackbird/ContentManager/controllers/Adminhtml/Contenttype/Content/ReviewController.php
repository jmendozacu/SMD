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

class Blackbird_ContentManager_Adminhtml_ContentType_Content_ReviewController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('blackbird');
        return $this;
    }
       
    public function indexAction() 
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_review'));
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $this->_redirect('*/*/');
    }
     
    public function newAction()
    {
        $this->_redirect('*/*/');
    }
     
    public function saveAction()
    {
        $this->_redirect('*/*/');
    }
    
    /*************************************************************************************************************************************************************/
    
    
    public function massDeleteAction()
    {
        $reviewIds = $this->getRequest()->getParam('review');
        
        if (!is_array($reviewIds)) {
            $this->_getSession()->addError($this->__('Please select review(s).'));
        } else {
            if (!empty($reviewIds)) {
                try {
                    foreach ($reviewIds as $reviewId) {
                        $review = Mage::getSingleton('contentmanager/review')->load($reviewId);
                        Mage::dispatchEvent('contenttype_controller_review_delete', array('review' => $review));
                        $review->delete();
                    }
                    $this->_getSession()->addSuccess(
                                    $this->__('Total of %d record(s) have been deleted.', count($reviewIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction()
    {
        $reviewIds = (array)$this->getRequest()->getParam('review');
        $status     = (int)$this->getRequest()->getParam('status');
    
        try {
            foreach ($reviewIds as $reviewId) {
                $review = Mage::getSingleton('contentmanager/review')->load($reviewId);
                Mage::dispatchEvent('contenttype_controller_review_update_status', array('review' => $review));
                $review->setStatus($status);
                $review->save();
            }
            $this->_getSession()->addSuccess(
                            $this->__('Total of %d record(s) have been updated.', count($reviewIds))
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
            ->addException($e, $this->__('An error occurred while updating the review(s) status.'));
        }
    
        $this->_redirect('*/*/index');
    }
}