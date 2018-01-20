<?php

class Epicor_Quotes_Adminhtml_Quotes_QuotesController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    protected $_aclId = 'sales/quotes';

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {

        $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
        /* @var $quote Epicor_Quotes_Model_Quote */

        $quote->getCustomer(true);
        Mage::register('quote', $quote);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {

        $successMsg = $this->__('Quote has been saved');
        $errorMsg = $this->__('Error occurred while trying to save the quote');
        $error = true;

        if ($this->savePost()) {

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));
            $errorMsg = '';
            $error = false;
        }

        $this->getResponse()->setBody(
            json_encode(
                array(
                    'redirectUrl' => Mage::helper("adminhtml")->getUrl('*/*/'),
                    'error' => $error,
                    'errorMsg' => $errorMsg
                )
            )
        );
    }

    private function savePost()
    {
        $saved = false;
        if ($data = $this->getRequest()->getPost()) {
            try {

                $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
                /* @var $quote Epicor_Quotes_Model_Quote */
                $prices = json_decode($data['prices'], true);
                $qtys = json_decode($data['qtys'], true);
                $noteText = $this->getRequest()->getPost('note');
                $quote->setSendAdminComments($this->getRequest()->getPost('send_comments') == 'true');
                $quote->setSendAdminReminders($this->getRequest()->getPost('send_reminders') == 'true');
                $quote->setSendAdminUpdates($this->getRequest()->getPost('send_updates') == 'true');
                $quote->setIsGlobal($this->getRequest()->getPost('is_global') == 'true');
                
                if (!empty($noteText)) {

                    $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
                    $visible = ($this->getRequest()->get('state') == Epicor_Quotes_Model_Quote_Note::STATE_PUBLISH_NOW);
                    $private = ($this->getRequest()->get('state') == Epicor_Quotes_Model_Quote_Note::STATE_PRIVATE);
                    $quote->addNote($noteText, $adminId, $visible, $private, false);
                }

                foreach ($quote->getProducts() as $product) {
                    /* @var $product Epicor_Quotes_Model_Quote_Product */
                    $quote->setProductNewPrice($prices[$product->getId()], $product->getId());
                    $quote->setProductNewQty($qtys[$product->getId()], $product->getId());
                }

                $quote->save();

                $saved = true;
            } catch (Exception $e) {
                $saved = false;
            } catch (Mage_Exception $e) {
                $saved = false;
            }
        }
        return $saved;
    }

    public function updatetotalsAction()
    {
        $errorMsg = $this->__('Error occurred while trying to calculate totals.');
        $error = true;
        $subtotal = 0;
        $taxTotal = 0;
        $products = array();
        if ($this->getRequest()->getPost('prices')) {
            try {
                $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
                /* @var $quote Epicor_Quotes_Model_Quote */
                $currencyCode = $quote->getcurrencyCode();
                $prices = json_decode($this->getRequest()->getPost('prices'), true);
                $qtys = json_decode($this->getRequest()->getPost('qtys'), true);

                foreach ($quote->getProducts() as $product) {
                    /* @var $product Epicor_Quotes_Model_Quote_Product */
                    $price = $prices[$product->getId()] * $qtys[$product->getId()];
                    $taxTotal += $quote->getProductTax($product->getProduct(), $price);
                    $subtotal += $price;
                    $products[$product->getId()] = Mage::helper('quotes')->formatPrice($price, true, $currencyCode);
                }
                $error = false;
                $errorMsg = '';
            } catch (Exception $e) {
                $error = true;
            }
        }

        $this->getResponse()->setBody(
            json_encode(
                array(
                    'error' => $error,
                    'errorMsg' => $errorMsg,
                    'products' => $products,
                    'subtotal' => Mage::helper('quotes')->formatPrice($subtotal, true, $currencyCode),
                    'taxTotal' => Mage::helper('quotes')->formatPrice($taxTotal, true, $currencyCode),
                    'grandTotal' => Mage::helper('quotes')->formatPrice($subtotal + $taxTotal, true, $currencyCode),
                )
            )
        );
    }

    public function reactivateAction()
    {
        $successMsg = $this->__('Quote has been reactivated');
        $errorMsg = $this->__('Error occurred while trying to reactivate the quote');

        try {
            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */

            $daysTillExpired = Mage::getStoreConfig('epicor_quotes/general/days_till_expired')? : 5;
            $quote->setExpires(strtotime('+' . $daysTillExpired . ' days'));
            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_PENDING_RESPONSE);
            $quote->save();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));
        } catch (Exception $e) {
            Mage::log(var_export($e, true),null,'exception.log');
            Mage::getSingleton('adminhtml/session')->addError($this->__($errorMsg));
        } catch (Mage_Exception $e) {
            Mage::log(var_export($e, true),null,'exception.log');
            Mage::getSingleton('adminhtml/session')->addError($this->__($errorMsg));
        }
        $this->_redirectReferer();
    }

    public function acceptAction()
    {
        if(!Mage::registry('gqr-accept')){
            Mage::register('gqr-accept', true);                                         // this stops the gqr being sent within the savePost) 
        }
        $successMsg = $this->__('Quote has been accepted');
        $errorMsg = $this->__('Error occurred while trying to accepted the quote');
        $error = true;
        try {
            if (!$this->savePost()) throw new Exception('Failed to Save Quote Data');
            Mage::unregister('gqr-accept');                                             // allows the gqr to be sent on next save()
            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_AWAITING_ACCEPTANCE);
            
            $quote->save();
            $time = time();
            foreach ($quote->getNonVisibleNotes() as $note) {
                $note->setIsVisible(true);
                $note->setCreatedAt($time);
                $note->save();
                $time++;
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));
            $error = false;
        } catch (Exception $e) {
            
        } catch (Mage_Exception $e) {
            
        }

        $this->getResponse()->setBody(
            json_encode(
                array(
                    'redirectUrl' => Mage::helper("adminhtml")->getUrl('*/*/'),
                    'error' => $error,
                    'errorMsg' => $errorMsg
                )
            )
        );
    }

    public function rejectAction()
    {
        $successMsg = $this->__('Quote has been rejected');
        $errorMsg = $this->__('Error occurred while trying to reject the quote');
        $error = true;
        try {
            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->setStatusId(Epicor_Quotes_Model_Quote::STATUS_QUOTE_REJECTED_ADMIN);
            $quote->save();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));
            $error = false;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($errorMsg));
        } catch (Mage_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($errorMsg));
        }

        if ($error) $this->_redirectReferer();
        else $this->_redirect('*/*/');
    }

    public function publishnoteAction()
    {

        $successMsg = $this->__('Comment has been saved');
        $errorMsg = $this->__('Error occurred while trying to save the comment');
        $error = true;
        $message = $errorMsg;
        $html = '';
        try {
            $note = Mage::getModel('quotes/quote_note')->load($this->getRequest()->get('id'));
            /* @var $note Epicor_Quotes_Model_Quote_Note */
            $note->setIsVisible(true);
            $note->setCreatedAt(time());
            $note->setSendEmail(true);
            $note->save();
            $quote = Mage::getModel('quotes/quote')->load($note->getQuoteId());
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->getCustomer(true);

            Mage::register('quote', $quote);
            $block = $this->getLayout()->createBlock('quotes/adminhtml_quotes_edit_commenthistory');
            $block->setTemplate('quotes/edit/commenthistory.phtml');
            $html = $block->toHtml();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));

            $error = false;
            $message = $successMsg;
        } catch (Exception $e) {
            
        } catch (Mage_Exception $e) {
            
        }
        $this->getResponse()->setBody(
            json_encode(
                array(
                    'replace' => 'quote-notes',
                    'error' => $error,
                    'message' => $message,
                    'html' => $html
                )
            )
        );
    }

    public function changenotestateAction()
    {

        $successMsg = $this->__('Comment has been saved');
        $errorMsg = $this->__('Error occurred while trying to save the comment');
        $error = true;
        $message = $errorMsg;
        $html = '';
        try {
            $note = Mage::getModel('quotes/quote_note')->load($this->getRequest()->get('id'));
            /* @var $note Epicor_Quotes_Model_Quote_Note */

            switch ($this->getRequest()->get('state')) {
                case Epicor_Quotes_Model_Quote_Note::STATE_PUBLISH_NOW:
                    $note->setIsVisible(true);
                    $note->setIsPrivate(false);
                    $note->setSendEmail(true);
                    //$note->setCreatedAt(time());
                    break;
                case Epicor_Quotes_Model_Quote_Note::STATE_PRIVATE:
                    $note->setIsVisible(false);
                    $note->setIsPrivate(true);
                    break;
            }
            $note->save();
            $quote = Mage::getModel('quotes/quote')->load($note->getQuoteId());
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->getCustomer(true);

            Mage::register('quote', $quote);
            $block = $this->getLayout()->createBlock('quotes/adminhtml_quotes_edit_commenthistory');
            $block->setTemplate('quotes/edit/commenthistory.phtml');
            $html = $block->toHtml();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($successMsg));

            $error = false;
            $message = $successMsg;
        } catch (Exception $e) {
            
        } catch (Mage_Exception $e) {
            
        }
        $this->getResponse()->setBody(
            json_encode(
                array(
                    'replace' => 'quote-notes',
                    'error' => $error,
                    'message' => $message,
                    'html' => $html
                )
            )
        );
    }

    public function submitnewnoteAction()
    {

        $successMsg = $this->__('Comment has been saved');
        $errorMsg = $this->__('Error occurred while trying to save the comment');
        $error = true;

        $html = '';
        try {
            $noteText = $this->getRequest()->get('note');

            if (!$noteText) throw new Exception('Comment textarea empty');

            $quote = Mage::getModel('quotes/quote')->load($this->getRequest()->get('id'));
            /* @var $quote Epicor_Quotes_Model_Quote */
            $quote->getCustomer(true);

            $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
            $email = ($this->getRequest()->get('state') == Epicor_Quotes_Model_Quote_Note::STATE_PUBLISH_NOW);
            $visible = ($this->getRequest()->get('state') == Epicor_Quotes_Model_Quote_Note::STATE_PUBLISH_NOW);
            $private = ($this->getRequest()->get('state') == Epicor_Quotes_Model_Quote_Note::STATE_PRIVATE);

            $quote->addNote($noteText, $adminId, $visible, $private, $email);

            $quote->save();

            $quote->refreshNotes();

            Mage::register('quote', $quote);
            $block = $this->getLayout()->createBlock('quotes/adminhtml_quotes_edit_commenthistory');
            $block->setTemplate('quotes/edit/commenthistory.phtml');
            $html = $block->toHtml();
            $error = false;
            $message = $successMsg;
        } catch (Exception $e) {
            $message = $errorMsg;
        } catch (Mage_Exception $e) {
            $message = $errorMsg;
        }

        $this->getResponse()->setBody(
            json_encode(
                array(
                    'replace' => 'quote-notes',
                    'error' => $error,
                    'message' => $message,
                    'html' => $html
                )
            )
        );
    }

}
