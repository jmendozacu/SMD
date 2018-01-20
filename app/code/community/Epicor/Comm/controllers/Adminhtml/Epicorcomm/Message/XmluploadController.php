<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Message_XmluploadController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function uploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $xmlHelper = Mage::helper('epicor_common/xml');
            /* @var $xmlHelper Epicor_Common_Helper_Xml */
            $messageHelper = Mage::helper('epicor_comm/messaging');
            /* @var $messageHelper Epicor_Comm_Helper_Messaging */
            $data = $this->getRequest()->getPost();
            Mage::register('posted_xml_data', $data, true);

            if ($data['input_type'] == Epicor_Comm_Block_Adminhtml_Message_Xmlupload_Form::XML_FILE_UPLOAD) {
                $xml_file = $_FILES['xml_file']['tmp_name'];
                $xml_message = file_get_contents($xml_file);
            } else if ($data['input_type'] == Epicor_Comm_Block_Adminhtml_Message_Xmlupload_Form::XML_TEXT_UPLOAD) {
                $xml_message = $data['xml_message'];
                Mage::register('posted_data', $data);
            }

            $xml = trim(preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml_message));

            try {
                $messageObj = $xmlHelper->convertXmlToVarienObject($xml);
                if ($messageObj !== false && $messageObj->getMessages() && $messageObj->getMessages()->getRequest()) {

                    if (count($messageObj->getMessages()->getRequest()) == 1) {

                        $response = $messageHelper->processSingleMessage($xml);
                        if (!$response->getIsValidXml()) {
                            $this->_getSession()->addError('Invalid XML (100)');
                        } elseif (!$response->getIsSuccessful()) {
                            $this->_getSession()->addError($response->getMsg());
                        } else {
                            $this->_getSession()->addSuccess('XML Message process completed successfully');
                        }
                    } else {
                        $index = 1;
                        $error = false;
                        foreach ($messageObj->getMessages()->getRequest() as $messageItem) {
                            if (!$messageItem->get_attributes()) {
                                continue;
                            }
                            $messageItem_Obj = new Varien_Object(array('messages' => new Varien_Object(array('request' => $messageItem))));
                            $messageItem_Xml = $xmlHelper->convertVarienObjectToXml($messageItem_Obj);

                            $response = $messageHelper->processSingleMessage($messageItem_Xml);
                            if (!$response->getIsValidXml() || !$response->getIsSuccessful()) {
                                $error = true;
                                $this->_getSession()->addError('Message ' . $index . ' : ' . $response->getMsg());
                            }
                            $index++;
                        }
                        if ($error) {
                            $this->_getSession()->addWarning('XML Message processing completed with errors');
                        } else {
                            $this->_getSession()->addSuccess('XML Message processing complete');
                        }
                    }
                } else {
                    $this->_getSession()->addError('Invalid XML (103)');
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

}
