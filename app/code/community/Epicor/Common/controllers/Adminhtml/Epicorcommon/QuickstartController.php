<?php

class Epicor_Common_Adminhtml_Epicorcommon_QuickstartController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function indexAction()
    {
        $this->_title($this->__('Quick Start'));
        $this->_storeId = Mage::app()->getStore()->getId();
        Mage::getConfig()->reinit();
        Mage::register('quickstartData', Epicor_Common_Helper_Quickstart::$CONFIG_FIELDS);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $oldErp = Mage::getStoreConfig('Epicor_Comm/licensing/erp');
        $params = $this->getRequest()->getParams();
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */

        unset($params['key']);
        unset($params['form_key']);

        $error = false;
        try {
            $mapping_data = new Varien_Object();

            foreach (Epicor_Common_Helper_Quickstart::$CONFIG_FIELDS as $config) {
                foreach ($config['fields'] as $key => $field) {
                    if (strpos($key, 'mapping') === 0) {
                        $mapping_data->setData(substr($key, 8), $helper->arrayToVarian($field));
                    }
                }
            }
            foreach ($params as $key1 => $value1) {

                if ($key1 == 'mapping') {
                    foreach ($value1 as $key2 => $value2) {
                        $orig_data = $value2['origData'];
                        unset($value2['origData']);
                        $element = $mapping_data->getData($key2);

                        foreach ($value2 as $id => $fields) {
                            if ($id == 'addRow') {
                                foreach ($fields as $newId => $newFields) {
                                    $model = Mage::getModel($element->getSourceModel());
                                    $mappingFields = $element->getMappingFields();
                                    foreach ($newFields as $key3 => $value3) {
                                        if (is_array($value3) && $mappingFields[$key3]['type'] == 'multiselect')
                                            $value3 = implode(', ', $value3);

                                        $model->setData($key3, $value3);
                                    }
                                    if ($element->getFieldsToFilter()) {
                                        foreach ($element->getFieldsToFilter()->getData() as $filter_field => $filter_value) {
                                            $model->setData($filter_field, $filter_value);
                                        }
                                    }
                                    try {
                                        $model->save();
                                    } catch (Exception $e) {
                                        $error[] = $e->getMessage();
                                    }
                                }
                            } elseif ($id == 'deleteRow' && $fields != '') {
                                $ids = explode(',', $fields);
                                foreach ($ids as $rowId) {
                                    $model = Mage::getModel($element->getSourceModel())->load($rowId);
                                    if ($model->getId()) {
                                        try {
                                            $model->delete();
                                        } catch (Exception $e) {
                                            $error[] = $e->getMessage();
                                        }
                                    }
                                }
                            } elseif (is_array($fields)) {
                                $model = Mage::getModel($element->getSourceModel())->load($id);
                                $mappingFields = $element->getMappingFields();
                                foreach ($fields as $key3 => $value3) {
                                    if (is_array($value3) && $mappingFields[$key3]['type'] == 'multiselect')
                                        $value3 = implode(', ', $value3);
                                    $model->setData($key3, $value3);
                                }
                                try {
                                    $model->save();
                                } catch (Exception $e) {
                                    $error[] = $e->getMessage();
                                }
                            }
                        }
                    }
                } else {

                    $groups = array();

                    foreach ($value1 as $key2 => $value2) {
                        $groups[$key2] = array(
                            'fields' => array()
                        );
                        foreach ($value2 as $key3 => $value3) {
                            $path = $key1 . '/' . $key2 . '/' . $key3;

                            $groups[$key2]['fields'][$key3]['value'] = $value3;
                        }
                    }
                    if (isset($_FILES[$key1]['name']) && is_array($_FILES[$key1]['name'])) {
                        /**
                         * Carefully merge $_FILES and $_POST information
                         * None of '+=' or 'array_merge_recursive' can do this correct
                         */
                        foreach ($_FILES[$key1]['name'] as $groupName => $group) {
                            if (is_array($group)) {
                                foreach ($group as $fieldName => $field) {
                                    if (!empty($field)) {
                                        $groups[$groupName]['fields'][$fieldName] = array('value' => $field);
                                    }
                                }
                            }
                        }
                    }


                    Mage::getSingleton('adminhtml/config_data')
                            ->setSection($key1)
                            ->setWebsite(null)
                            ->setStore(null)
                            ->setGroups($groups)
                            ->save();
                }
            }

            Mage::getModel('epicor_common/config_backend_erps')->afterSave(true, true);

        } catch (Exception $e) {
            $error[] = $e->getMessage();
        } catch (Mage_Exception $e) {
            $error[] = $e->getMessage();
        }
        if (is_array($error)) {
            foreach ($error as $err) {
                Mage::getSingleton('adminhtml/session')->addError($err);
            }
        } else {

            Mage::getSingleton('adminhtml/session')->addSuccess('Settings Updated');
        }

        // have to do this otherwise magento won't pick up the updated config values
        Mage::getConfig()->reinit();

        $this->_redirect('*/*/index');
    }

    public function networktestAction()
    {
        $url = $this->getRequest()->get('url');
        $user = $this->getRequest()->get('user');
        $pass = $this->getRequest()->get('pass');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $result = $helper->connectionTest($url, $user, $pass);

        Mage::app()->getResponse()->setBody($result);
    }

    public function requestlicenseAction()
    {
        $url = $this->getRequest()->get('url');
        $user = $this->getRequest()->get('user');
        $pass = $this->getRequest()->get('pass');

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $result = $helper->requestLicense($url, $user, $pass);

        Mage::app()->getResponse()->setBody($result);
    }
    
    public function p21tokenAction() {
        $url = $this->getRequest()->get('url');
        $user = $this->getRequest()->get('user');
        $pass = $this->getRequest()->get('pass');
        
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        Mage::app()->getResponse()->setBody($helper->getP21Token($url, $user, $pass));
    }

    public function customer_settingsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function products_configurator_settingsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function checkout_settingsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function b2b_settingsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

}
