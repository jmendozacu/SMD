<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Adminhtml_Amgdpr_WithoutConsentController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        Mage::getSingleton('amgdpr/consentQueue')->addQueueLinkNotice();
        $this->loadLayout();
        $this->_setActiveMenu('customer/amgdpr/without_consent_log');
        $this->_title($this->__('Customers Without Consent'));
        $this->_addBreadcrumb($this->__('Customers Without Consent'), $this->__('Customers Without Consent'));
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_withoutConsent');
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
        $customerIds = $this->getRequest()->getParam('without_consents');
        Mage::getResourceModel('amgdpr/consentQueue_collection')->insertIds($customerIds);

        $this->_getSession()->addSuccess($this->__('Customers were successfully added to email queue'));

        $this->_redirect('*/*');
    }

    protected function _exportGrid($type)
    {
        $fileName = 'amasty_without_consent_export.' . $type;
        $block = $this->getLayout()->createBlock('amgdpr/adminhtml_withoutConsent_grid');
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
        return Mage::getSingleton('admin/session')->isAllowed('customer/amgdpr/without_consent_log');
    }
}
