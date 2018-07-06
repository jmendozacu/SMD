<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Block_Settings extends Mage_Core_Block_Template
{
    public function getPrivacySettings()
    {
        return array(
            array(
                'title' => $this->__('Download personal data'),
                'content' => $this->__('Here you can download a copy of your personal data which we store for your account in CSV format.'),
                'hasCheckbox' => false,
                'checkboxText' => '',
                'submitText' => $this->__('Download'),
                'action' => $this->getUrl('amgdpr/customer/downloadCsv'),
            ),
            array(
                'title' => $this->__('Anonymise personal data'),
                'content' => $this->__('Anonymising your personal data means that it will be replaced with non-personal anonymous information and before that you will get your new login and password to your e-mail address. After this process, your e-mail address and all other personal data will be removed from the website.'),
                'hasCheckbox' => true,
                'checkboxText' => $this->__('I agree and I want to proceed'),
                'submitText' => $this->__('Proceed'),
                'action' => $this->getUrl('amgdpr/customer/anonymise'),
            ),
            array(
                'title' => $this->__('Delete account'),
                'content' => $this->__('Request to remove your account, together with all your personal data, will be processed by our staff.<br>Deleting your account will remove all the purchase history, discounts, orders, invoices and all other information that might be related to your account or your purchases.<br>All your orders and similar information will be lost.<br>You will not be able to restore access to your account after we approve your removal request.'),
                'checked' => true,
                'hasCheckbox' => true,
                'checkboxText' => $this->__('I understand and I want to delete my account'),
                'submitText' => $this->__('Submit request'),
                'action' => $this->getUrl('amgdpr/customer/deleteRequest'),
            )
        );
    }
}
