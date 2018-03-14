<?php

class Epicor_Comm_Block_Catalog_Product_Ewa extends Mage_Core_Block_Template
{

    protected $_ewaData;
    protected $_cimData;

    private function getEWAData()
    {
        if (!$this->_ewaData)
            $this->_ewaData = Mage::registry('EWAData');
        return $this->_ewaData;
    }

    private function getCIMData()
    {
        if (!$this->_cimData)
            $this->_cimData = Mage::registry('CIMData');
        return $this->_cimData;
    }

    private function getEWASku()
    {
        return Mage::registry('EWASku');
    }

    public function hasEWAData()
    {
        return (bool) $this->getEWAData();
    }

    public function getFormUrl()
    {
        return Mage::getStoreConfig('epicor_comm_enabled_messages/cim_request/ewa_url');
    }

    public function getConfigName()
    { //<CompanyID>_VER<ConfigVersion>._<ConfigID>
        $configName = $this->getEWAData()->getConfigName();
        return $configName->getCompanyId() . '_VER' . $configName->getConfigVersion() . '.' . $configName->getConfigId();
    }

    public function getRelatedToTable()
    {
        return $this->getEWAData()->getRelatedToTable();
    }

    public function getRelatedToRowID()
    {
        return $this->getEWAData()->getRelatedToRowID();
    }

    public function getPartNum()
    {
        return $this->getEWASku(); //$this->getEWAData()->getProductCode();
    }

    public function getPartRev()
    {
        return $this->getEWAData()->getProductRevision();
    }

    public function getGroupSequence()
    {
        return $this->getEWAData()->getGroupSequence();
    }

    public function getStyleSheet()
    {
        $file = Mage::getDesign()->getFilename('css/ewa.css', array('_type' => 'skin'));
        $url = Mage::getDesign()->getSkinUrl('css/ewa.css');

        if (!file_exists($file))
            $url = Mage::getUrl('epicor_comm/configurator/ewacss');

        return $url;
    }

    public function getLanguage()
    {
        $helper = $this->helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        return $helper->getLanguageMapping(Mage::getStoreConfig('general/locale/code'));
    }

    public function getReturnUrl()
    {

        $helper = $this->helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        if (!Mage::registry('EWAReturn')) {
            $route = 'epicor_comm/configurator/ewacomplete';
        } else {
            $route = base64_decode(Mage::registry('EWAReturn'));
        }

        $params = array(
            'SKU' => $helper->urlEncode($this->getPartNum()),
            'EWACode' => $helper->urlEncode($this->getRelatedToRowID()),
            'GroupSequence' => $helper->urlEncode($this->getGroupSequence()),
            'location' => $helper->urlEncode(Mage::registry('location_code')),
            'qty' => $helper->urlEncode(Mage::app()->getRequest()->getParam('qty')),
        );

        if ($this->getCIMData()->getQuoteId()) {
            $params['quoteId'] = $helper->urlEncode($this->getCIMData()->getQuoteId());
        }

        if ($this->getCIMData()->getLineNumber()) {
            $params['lineNumber'] = $helper->urlEncode($this->getCIMData()->getLineNumber());
        }

        if ($this->getCIMData()->getItemId()) {
            $params['itemId'] = $helper->urlEncode($this->getCIMData()->getItemId());
        }

        $url = Mage::getUrl($route, $params);

        return $url;
    }

    public function getECCUser()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        $username = Mage::getStoreConfig('Epicor_Comm/licensing/ewa_username');
        return $helper->eccEncode($username);
    }

    public function getECCPwd()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        $password = $helper->decrypt(Mage::getStoreConfig('Epicor_Comm/licensing/ewa_password'));
        return $helper->eccEncode($password);
        #return md5($helper->decrypt(Mage::getStoreConfig('Epicor_Comm/licensing/password')));
    }

    public function getECCCompanyId()
    {
        return Mage::app()->getStore()->getWebsite()->getCompany() ?: Mage::app()->getStore()->getGroup()->getCompany();
    }

}
