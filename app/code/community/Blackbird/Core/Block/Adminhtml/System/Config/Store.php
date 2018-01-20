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
 * @version		1.1.1
 */

class Blackbird_Core_Block_Adminhtml_System_Config_Store
    extends Mage_Adminhtml_Block_Template
    implements Varien_Data_Form_Element_Renderer_Interface
{
	
    public function render(Varien_Data_Form_Element_Abstract $fieldset)
    {
        return $this->getBlackbirdStore();
    }
    
    public function getBlackbirdStore()
    {
    	$response = Mage::app()->loadCache("blackbird_core_store");
    	if (!$response){
	    	$url = "http://black.bird.eu/distant-about.php";
	    	
	        $curl = new Varien_Http_Adapter_Curl();
	        $curl->setConfig(array('timeout' => 10));
	        $curl->write(Zend_Http_Client::GET, $url, '1.0');
	        
	        $response = $curl->read();
	
	        if ($response !== false) {
	            $response = preg_split('/^\r?$/m', $response, 2);
	            $response = trim($response[1]);
	            Mage::app()->saveCache($response, "blackbird_core_store");
	        }
	        else {
	            $response =  Mage::app()->loadCache("blackbird_core_store");
	            if (!$response) {
	                Mage::getSingleton('adminhtml/session')->addError(
	                	$this->__("Sorry but Blackbird addons website is not available. Please try again or contact magento@blackbird.fr")
	                );
	            }
	        }
	        $curl->close();
    	}
    	
    	$this->_data = $response;
	    return $this->_data;
    }
    
}
