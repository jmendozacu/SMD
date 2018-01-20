<?php
/**
 * Faqs adminhtml controller
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 *
 */
class Epicor_Faqs_Adminhtml_FaqsController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    //protected $_aclId = 'epicor_common/faqs';
    
    /**
     * Init actions
     *
     * @return Epicor_Faqs_Adminhtml_FaqsController
     */
    protected function _initAction()
    {
        // Load layout, set active menu and breadcrumbs
       $this->loadLayout()->_setActiveMenu('epicor_common')
            ->_addBreadcrumb(
                  Mage::helper('epicor_faqs')->__('F.A.Q.'),
                  Mage::helper('epicor_faqs')->__('F.A.Q.')
              )
            ->_addBreadcrumb(
                  Mage::helper('epicor_faqs')->__('Manage F.A.Q.'),
                  Mage::helper('epicor_faqs')->__('Manage F.A.Q.')
              );

        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('F.A.Q.'))
             ->_title($this->__('Manage F.A.Q.'));
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new Faqs item
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit Faqs item
     */
    public function editAction()
    {
        $this->_title($this->__('F.A.Q.'))
             ->_title($this->__('Manage F.A.Q.'));

        // 1. instance faqs model
        /* @var $model Epicor_Faqs_Model_Item */
        $model = Mage::getModel('epicor_faqs/faqs');

        // 2. if exists id, check it and load data
        $faqsId = $this->getRequest()->getParam('id');
        if ($faqsId) {
            
            $model->load($faqsId);
            $model->setStores(explode(',',$model->getStores()));
 
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('epicor_faqs')->__('F.A.Q. item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('epicor_faqs')->__('Edit Item');
        } else {
            $this->_title(Mage::helper('epicor_faqs')->__('New Item'));
            $breadCrumb = Mage::helper('epicor_faqs')->__('New Item');
        }

        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('faqs_item', $model);

        // 5. render layout
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            /* @var $model Epicor_Faqs_Model_Item */
            $model = Mage::getModel('epicor_faqs/faqs');

            // if faqs item exists, try to load it
            $faqsId = $this->getRequest()->getParam('id');
            if ($faqsId) {
                $model->load($faqsId);
            }

            $model->addData($data);

            try {
                $hasError = false;
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('epicor_faqs')->__('The F.A.Q. has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('epicor_faqs')->__('An error occurred while saving the F.A.Q. item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model Epicor_Faqs_Model_Item */
                $model = Mage::getModel('epicor_faqs/faqs');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_faqs')->__('Unable to find a F.A.Q. item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('epicor_faqs')->__('The F.A.Q. has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('epicor_faqs')->__('An error occurred while deleting the F.A.Q. item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('faqs/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('faqs/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('faqs/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data['stores']=implode(',',$data['stores']);
        $data = $this->_filterDates($data, array('time_published'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}