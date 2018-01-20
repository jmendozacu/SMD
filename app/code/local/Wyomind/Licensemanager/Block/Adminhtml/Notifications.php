<?php

class Wyomind_Licensemanager_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Notification_Toolbar
{
    const SOAP_URL = "https://www.wyomind.com/services/licenses/webservice.soap.php";
    const SOAP_URI = "https://www.wyomind.com/";
    const WS_URL = "https://www.wyomind.com/license_activation/?licensemanager=%s&";

    private $_messages = array(
        "activation_key_warning" => "Your activation key is not yet registered.<br><a href='%s'>Go to system > configuration > Wyomind > %s</a>.",
        "license_code_warning" => "Your license is not yet activated.<br><a target='_blank' href='%s'>Activate it now !</a>",
        "license_code_updated_warning" => "Your license must be re-activated.<br><a target='_blank' href='%s'>Re-activate it now !</a>",
        "ws_error" => "The Wyomind's license server encountered an error.<br><a target='_blank' href='%s'>Please go to Wyomind license manager</a>",
        "ws_success" => "<b style='color:green'>%s</b>",
        "ws_failure" => "<b style='color:red'>%s</b>",
        "ws_no_allowed" => "Your server doesn't allow remote connections.<br><a target='_blank' href='%s'>Please go to Wyomind license manager</a>",
        "upgrade" => "<u>Extension upgrade from v%s to v%s</u>.<br> Your license must be updated.<br>Please clean all caches and reload this page.",
        "license_warning" => "License Notification",
    );
    private $_warnings = array();
    private $_refreshCache = false;

    public function __construct() 
    {
        foreach ($this->getValues() as $ext) {
            $this->checkActivation($ext);
        }

        if ($this->_refreshCache) {
            Mage::getConfig()->cleanCache();
        }

        if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
            if ($this->_toHtml(null) != null) {
                Mage::getSingleton('core/session')->addNotice($this->_toHtml(null));
            }
        }
    }

    public function XML2Array($xml) 
    {
        $newArray = array();
        $array = (array) $xml;
        foreach ($array as $key => $value) {
            $value = (array) $value;
            if (isset($value [0])) {
                $newArray [$key] = trim($value [0]);
            } else {
                $newArray [$key] = $this->XML2Array($value, true);
            }
        }
        return $newArray;
    }

    public function getValues() 
    {
        $dir = Mage::getBaseDir() . '/app/code/local/Wyomind/';
        $ret = array();
        if (is_dir($dir)) {
            if (($dh = opendir($dir)) != false) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($dir . $file) && $file != '.' && $file != '..' && $file != 'Notificationmanager') {
                        if (is_file($dir . $file . '/etc/system.xml')) {
                            $xml = simplexml_load_file($dir . $file . '/etc/system.xml');
                            $namespace = strtolower($file);
                            $labelArray = $this->XML2Array($xml);
                            $label = $labelArray['sections'][$namespace]['label'];
                            if (Mage::getConfig()->getModuleConfig('Wyomind_' . $file)->is('active', 'true')) {
                                $ret[] = array('label' => $label, 'value' => $file);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        return $ret;
    }

    protected function sprintf_array($format, $arr) 
    {
        return call_user_func_array('sprintf', array_merge((array) $format, $arr));
    }

    protected function addWarning($name, $type, $vars = array(), $success = false) 
    {
        if ($type) {
            $output = $this->sprintf_array($this->_messages[$type], $vars);
        } else {
            $output = implode(' ' . $vars);
        }
        
        $output = "<b> Wyomind " . $name . "</b> <br> " . $output . "";

        if ($success) {
            $this->_warnings[] = $output;
        } else {
            $this->_warnings[] = $output;
        }
    }

    protected function checkActivation($extension) 
    {
        $wsUrl = sprintf(self::WS_URL, Mage::getConfig()->getNode('modules/Wyomind_Licensemanager')->version);
        $activationKey = Mage::getStoreConfig(strtolower($extension['value']) . '/license/activation_key');
        $licensingMethod = Mage::getStoreConfig(strtolower($extension['value']) . '/license/get_online_license');
        $licenseCode = Mage::getStoreConfig(strtolower($extension['value']) . '/license/activation_code');
        $domain = Mage::getStoreConfig('web/secure/base_url');

        $registeredVersion = (string) Mage::getStoreConfig(strtolower($extension['value']) . '/license/version');
        $currentVersion = (string) Mage::getConfig()->getNode('modules/Wyomind_' . $extension['value'])->version;
        $lowerName = strtolower($extension['value']);
        
        $soapParams = array(
            'method' => 'get',
            'rv' => $registeredVersion,
            'cv' => $currentVersion,
            'namespace' => $extension['value'],
            'activation_key' => $activationKey,
            'domain' => $domain,
            'magento' => Mage::getVersion(),
            'licensemanager' => Mage::getConfig()->getNode('modules/Wyomind_Licensemanager')->version
        );

        $wsParam = '&rv=' . $registeredVersion . '&cv=' . $currentVersion . '&namespace=' . $extension['value'] 
                . '&activation_key=' . $activationKey . '&domain=' . $domain . '&magento=' . Mage::getVersion();

        // Extension upgrade
        if ($registeredVersion != $currentVersion && $licenseCode) {
            Mage::getConfig()->saveConfig($lowerName . '/license/version', $currentVersion, 'default', '0');
            Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
            Mage::helper('licensemanager')->log(
                $extension['value'], $registeredVersion, $domain, $activationKey, 'upgrade to v' . $currentVersion
            );
            $this->addWarning($extension['label'], 'upgrade', array($registeredVersion, $currentVersion));
            Mage::getSingleton('core/session')->setData('update_' . $extension['value'], 'true');
            $this->_refreshCache = true;
        } elseif (!$activationKey) {
        // no activation key not yet registered
            Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
            Mage::helper('licensemanager')->log(
                $extension['value'], $registeredVersion, $domain, $activationKey, 'activation key -> pending'
            );
            
            $warningUrl = Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/' . $lowerName . '/');
            
            $this->addWarning(
                $extension['label'], 
                'activation_key_warning', 
                array($warningUrl, ($extension['label']))
            );
        } elseif ($activationKey && (!$licenseCode || empty($licenseCode)) && !$licensingMethod) {
            // not yet activated --> manual activation
            Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
            Mage::helper('licensemanager')->log(
                $extension['value'], $registeredVersion, $domain, $activationKey, 'manual activation -> pending'
            );
            if (Mage::getSingleton('core/session')->getData('update_' . $extension['value']) != 'true') {
                $this->addWarning(
                    $extension['label'], 'license_code_warning', array($wsUrl . 'method=post' . $wsParam)
                );
            } else {
                $this->addWarning(
                    $extension['label'], 'license_code_updated_warning', array($wsUrl . 'method=post' . $wsParam)
                );
            }
        } elseif ($activationKey && (!$licenseCode || empty($licenseCode)) && $licensingMethod) {
            // not yet activated --> automatic activation
            try {
                $options = array('location' => self::SOAP_URL, 'uri' => self::SOAP_URI);
                if (!class_exists('SoapClient')) {
                    throw new Exception();
                }
                $api = new \SoapClient(NULL, $options);
                $ws = $api->checkActivation($soapParams);
                $wsResult = json_decode($ws);
                
                switch ($wsResult->status) {
                    case 'success' :
                        $this->addWarning($extension['label'], 'ws_success', array($wsResult->message), true);
                        Mage::getConfig()->saveConfig(
                            $lowerName . '/license/version', $wsResult->version, 'default', '0'
                        );
                        Mage::getConfig()->saveConfig(
                            $lowerName . '/license/activation_code', $wsResult->activation, 'default', '0'
                        );
                        Mage::helper('licensemanager')->log(
                            $extension['value'], $registeredVersion, $domain, $activationKey, 
                            'automatic activation -> success:' . $wsResult->message
                        );
                        $this->_refreshCache = true;
                        break;
                    case 'error' :
                        $this->addWarning($extension['label'], 'ws_failure', array($wsResult->message));
                        Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
                        Mage::helper('licensemanager')->log(
                            $extension['value'], $registeredVersion, $domain, $activationKey, 
                            'automatic activation -> error:' . $wsResult->message
                        );

                        $this->_refreshCache = true;
                        break;
                    default :
                        $this->addWarning($extension['label'], 'ws_error', array($wsUrl . 'method=post' . $wsParam));
                        Mage::helper('licensemanager')->log(
                            $extension['value'], $registeredVersion, $domain, $activationKey, 
                            'automatic activation -> unknown:' . $wsResult->message
                        );
                        Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
                        Mage::getConfig()->saveConfig($lowerName . '/license/get_online_license', '0', 'default', '0');    
                        $this->_refreshCache = true;
                        break;
                }
            } catch (Exception $e) {
                $this->addWarning($extension['label'], 'ws_no_allowed', array($wsUrl . 'method=post' . $wsParam));
                Mage::helper('licensemanager')->log(
                    $extension['value'], $registeredVersion, $domain, $activationKey, 
                    'automatic activation -> ' . $e->getMessage()
                );
                Mage::getConfig()->saveConfig($lowerName . '/license/activation_code', '', 'default', '0');
                Mage::getConfig()->saveConfig($lowerName . '/license/get_online_license', '0', 'default', '0');    
                $this->_refreshCache = true;
            }
        }
    }

    public function _toHtml($className = 'notification-global') 
    {
        $html = null;
        foreach ($this->_warnings as $warning) {
            $html .= '<div class="notification-global">' . $warning . '</div>';
        }

        return $html;
    }
}