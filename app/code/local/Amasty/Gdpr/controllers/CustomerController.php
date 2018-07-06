<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_CustomerController extends Mage_Core_Controller_Front_Action
{
    const CSV_FILE_NAME = 'personal-data.csv';

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function anonymiseAction()
    {
        try {
            $anonymisator = Mage::getSingleton('amgdpr/anonymizeModel');
            $anonymisator->anonymizeCustomer($this->getSession()->getId());
            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Anonymisation was successful')
            );
        } catch (Exception $exception) {
            $this->getSession()->addError($this->__('An error has occurred'));
            Mage::logException($exception);
            $this->_redirect('*/*/settings');
        }


        $this->_redirectReferer();
    }

    public function downloadCsvAction()
    {
        /** @var Amasty_Gdpr_Model_CustomerData $customerData */
        $customerData = Mage::getSingleton('amgdpr/customerData');

        try {
            $data = $customerData->getPersonalData($this->getSession()->getId());

            ob_start();
            $out = fopen('php://output', 'w');

            foreach ($data as $row) {
                fputcsv($out, $row);
            }
            $csvContent = ob_get_clean();

            $this->_prepareDownloadResponse(
                self::CSV_FILE_NAME,
                $csvContent,
                'text/csv'
            );
        } catch (Exception $e) {
            $this->getSession()->addError($this->__('An error has occurred'));
            Mage::logException($e);
            $this->_redirect('*/*/settings');
        }
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function getSession()
    {
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');

        return $customerSession;
    }

    public function settingsAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $block = $this->getLayout()->getBlock('privacy_settings');

        try {
            if ($block) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->getLayout()->getBlock('head')->setTitle($this->__('Privacy Settings'));
            $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->renderLayout();
    }

    public function deleteRequestAction()
    {
        try {
            /** @var Amasty_Gdpr_Model_DeleteRequest $request */
            $request = Mage::getModel('amgdpr/deleteRequest');

            $customer = $this->getSession()->getCustomer();
            $request
                ->setData(array(
                    'customer_id' => $customer->getId(),
                    'customer_email' => $customer->getEmail(),
                    'customer_name' => $customer->getName(),
                ))
                ->save();

            Mage::getSingleton('amgdpr/actionLog')->logAction('delete_request_submitted', $customer->getId());

            if (Mage::getStoreConfig('amgdpr/deletion_notification/admin')
                && $to = Mage::getStoreConfig('amgdpr/deletion_notification/to')
            ) {
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);

                $sender = array(
                    'name' => $customer->getName(),
                    'email' => $customer->getEmail()
                );

                $tpl = Mage::getModel('core/email_template');
                $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$customer->getStore()->getId()))
                    ->sendTransactional(
                        Mage::getStoreConfig('amgdpr/deletion_notification/admin_template'),
                        $sender,
                        $to,
                        Mage::helper('amgdpr')->__('Administrator'),
                        array('customer' => $customer)
                    );
                $translate->setTranslateInline(true);
            }

            $this->getSession()->addSuccess(
                $this->__('Thank you, your account delete request was recorded.')
            );
        } catch (Exception $exception) {
            $this->getSession()->addError($this->__('An error has occurred'));
            Mage::logException($exception);
            $this->_redirect('*/*/settings');
        }

        $this->_redirectReferer();
    }
}
