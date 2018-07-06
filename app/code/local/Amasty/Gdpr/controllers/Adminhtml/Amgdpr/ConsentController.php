<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Adminhtml_Amgdpr_ConsentController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        Mage::getSingleton('amgdpr/consentQueue')->addQueueLinkNotice();
        $this->loadLayout();
        $this->_setActiveMenu('customer/amgdpr/consent_log');
        $this->_title($this->__('Customers With Consent'));
        $this->_addBreadcrumb($this->__('Customers With Consent'), $this->__('Customers With Consent'));
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_consent');
        $this->_addContent($block);
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $this->_exportGrid('csv');
    }

    public function exportXmlAction()
    {
        $this->_exportGrid('xml');
    }

    public function emailConsentAction()
    {
        $customerIds = $this->getRequest()->getParam('consents');

        /** @var Amasty_Gdpr_Model_Resource_ConsentLog_Collection $consentCollection */
        $consentCollection = Mage::getResourceModel('amgdpr/consentLog_collection');

        $consentCollection->addFieldToFilter('id', array('in' => $customerIds));

        Mage::getResourceModel('amgdpr/consentQueue_collection')->insertIds(
            $consentCollection->getColumnValues('customer_id')
        );

        $this->_getSession()->addSuccess($this->__('Customers were successfully added to email queue'));

        $this->_redirect('*/*');
    }

    protected function _exportGrid($type)
    {
        $fileName = 'amasty_consent_export.' . $type;
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_consent_grid');
        switch ($type) {
            case 'xml':
                $content = $block->getXml();
                break;

            case 'csv':
                $content = $block->getCsv();
                break;

            default:
                throw new Exception($this->__('Please specify export data type'));
                break;
        }

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/amgdpr/consent_log');
    }
}
