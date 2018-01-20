<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer login form block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Epicor_B2b_Block_Portal_Login extends Mage_Customer_Block_Form_Login
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Customer Login'));
        return parent::_prepareLayout();
    }

    public function showCustomerRegistration()
    {
        return Mage::getStoreConfigFlag('epicor_b2b/registration/reg_customer');
    }

    public function showBusinessRegistration()
    {
        return Mage::getStoreConfigFlag('epicor_b2b/registration/reg_portal');
    }
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->helper('customer')->getLoginPostUrl();
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getB2bCreateAccountUrl()
    {   
        return  Mage::getUrl('b2b/portal/register');
    }
}
