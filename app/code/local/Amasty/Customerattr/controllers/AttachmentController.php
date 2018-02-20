<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Customerattr
 */
class Amasty_Customerattr_AttachmentController
    extends Mage_Core_Controller_Front_Action
{
    public function downloadAction()
    {
        $customerId = $this->getRequest()->getParam('customer');
        if (Mage::getSingleton('customer/session')->isLoggedIn()
            && $customerId == Mage::getSingleton('customer/session')
                ->getCustomer()->getId()
        ) {
            $fileName = $this->getRequest()->getParam('file');
            $fileName = Mage::helper('core')->urlDecode($fileName);
            $downloadPath = Mage::helper('amcustomerattr')->getAttributeFileUrl(
                $fileName
            );
            $fileName = Mage::helper('amcustomerattr')->cleanFileName(
                $fileName
            );
            $downloadPath = $downloadPath . $fileName[1] . DS . $fileName[2]
                . DS;
            if (file_exists($downloadPath . $fileName[3])) {
                header(
                    'Content-Disposition: attachment; filename="' . $fileName[3]
                    . '"'
                );
                if (function_exists('mime_content_type')) {
                    header(
                        'Content-Type: ' . mime_content_type(
                            $downloadPath . $fileName[3]
                        )
                    );
                } else if (class_exists('finfo')) {
                    $finfo = new finfo(FILEINFO_MIME);
                    $mimetype = $finfo->file($downloadPath . $fileName[3]);
                    header('Content-Type: ' . $mimetype);
                }
                readfile($downloadPath . $fileName[3]);
            }
        }
    }

    public function forgotPasswordPostAction()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('customer/account/forgotpassword');
                    return;
                }
            }
            $this->_getSession()
                ->addSuccess( Mage::helper('customer')
                ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                    Mage::helper('customer')->escapeHtml($email)));
            $this->_redirect('customer/account/login');
            return;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('customer/account/forgotpassword');
            return;
        }
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
