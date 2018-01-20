<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {


        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_quotes';
        $this->_blockGroup = 'quotes';
        $this->_mode = 'edit';

        $quote = Mage::registry('quote');
        /* @var $quote Epicor_Quotes_Model_Quote */

        $this->_removeButton('delete');
        $this->_removeButton('save');

        if ($quote->isActive()) {
            $rejectUrl = Mage::helper('adminhtml')->getUrl(
                'adminhtml/quotes_quotes/reject/',
                array('id' => $quote->getId())
            );
            $this->_addButton(
                'rejectquote',
                array(
                    'label' => Mage::helper('adminhtml')->__('Reject Quote'),
                    'onclick' => 'window.location = \'' . $rejectUrl . '\'',
                    'class' => 'delete',
                ), 
                -100
            );

            $acceptUrl = Mage::helper('adminhtml')->getUrl(
                'adminhtml/quotes_quotes/accept/',
                array('id' => $quote->getId())
            );
            
            if ($quote->getStatusId() == Epicor_Quotes_Model_Quote::STATUS_PENDING_RESPONSE) {
                $this->_addButton(
                    'acceptquote',
                    array(
                        'label' => Mage::helper('adminhtml')->__('Accept Quote'),
                        'onclick' => 'quoteform.accept(\'' . $acceptUrl . '\')',
                        'class' => 'save',
                    ), 
                    -100
                );

                $saveUrl = Mage::helper('adminhtml')->getUrl(
                    'adminhtml/quotes_quotes/save/',
                    array('id' => $quote->getId())
                );
                
                $this->_addButton(
                    'savequote',
                    array(
                        'label' => Mage::helper('adminhtml')->__('Save Quote for Later'),
                        'onclick' => 'quoteform.save(\'' . $saveUrl . '\')',
                        'class' => 'save',
                    ), 
                    -90
                );
            }
        } elseif (!$quote->isActive() && $quote->getStatusId() != Epicor_Quotes_Model_Quote::STATUS_QUOTE_ORDERED) {

            if ($quote->getStatusId() == Epicor_Quotes_Model_Quote::STATUS_QUOTE_ACCEPTED) {
                $label = 'Retract Quote';
            } else { 
                $label = 'Re-Activate Quote';
            }

            $reactivateUrl = Mage::helper('adminhtml')->getUrl(
                'adminhtml/quotes_quotes/reactivate/',
                array('id' => $quote->getId())
            );
            $this->_removeButton('reset');
            $this->_addButton(
                'activatequote',
                array(
                    'label' => Mage::helper('adminhtml')->__($label),
                    'onclick' => 'window.location = \'' . $reactivateUrl . '\'',
                    'class' => 'save',
                ), 
                -100
            );
        }
    }

    public function getHeaderText()
    {
        $quote = Mage::registry('quote');
        /* @var $quote Epicor_Quotes_Model_Quote */
        if ($quote && $quote->getId()) {
            $title = $quote->getId();
            $customer = $quote->getCustomer(true);
            
            $header =  Mage::helper('adminhtml')->__(
                'Quote-%s %s (%s)', 
                $this->htmlEscape($title),
                $this->htmlEscape($customer->getName()), 
                $this->htmlEscape($customer->getEmail())
            );
        } else {
            $header =  Mage::helper('adminhtml')->__('Quote');
        }
        
        return $header;
    }

}
