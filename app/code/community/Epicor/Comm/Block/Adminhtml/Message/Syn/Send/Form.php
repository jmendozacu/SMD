<?php

/**
 * Epicor_Comm_Block_Adminhtml_Message_Syn
 * 
 * Form for SYN Send form
 * 
 * @author Gareth.James
 */
class Epicor_Comm_Block_Adminhtml_Message_Syn_Send_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/send'),
                'method' => 'post'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'layout_block_form', 
            array(
                'legend' => Mage::helper('epicor_comm')->__('Send SYN request')
            )
        );

        $fieldset->addField(
            'syn_stores', 
            'multiselect', array(
                'label' => Mage::helper('epicor_comm')->__('Store'),
                'required' => true,
                'values' => Mage::getModel('epicor_comm/config_source_sync_stores')->toOptionArray(),
                'name' => 'stores'
            )
        );

        $fieldset->addField(
            'messages0', 
            'multiselect', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Upload Messages'),
                'required' => true,
                'name' => 'simple_messages',
                'class' => 'simple_messages',
                'style' => 'float:left',
                'values' => $this->_getSimpleUploadMessages(),
            )
        );

        $fieldset->addField(
            'messages1', 
            'multiselect', array(
                'label' => Mage::helper('epicor_comm')->__('Upload Messages'),
                'required' => false,
                'name' => 'advanced_messages',
                'class' => 'advanced_messages scalable hidden',
                'values' => $this->_getUploadMessages(),
            )
        );
        $hidden_button = $fieldset->addField('msg_toggle_button', 'hidden', false);

        $hidden_button->setAfterElementHtml('
            <button id="toggle_button" type="button" onclick="toggleMessage()" style="float:right">Advanced</button>                                       
            <script>
                $("messages0").up("tr").selected=false;
                $("messages1").up("tr").style.display="none";               

                function toggleMessage() {
                   $("toggle_button").innerHTML = ($("toggle_button").innerHTML == "Advanced")? "Simple" : "Advanced"; 
                   $("messages1").toggleClassName("required-entry").up("tr").toggle();
                   $("messages0").toggleClassName("required-entry").up("tr").toggle();
                   $$("select#messages1 option").each(function(o){				
                        o.selected = false;
                    })
                }
            </script>'
        );
        
        $fieldset->addField(
            'languages', 
            'multiselect', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Languages'),
                'required' => true,
                'size' => 5,
                'name' => 'languages',
                'values' => $this->_getLanguages(),
            )
        );

        $syncType = $fieldset->addField(
            'sync_type', 
            'select', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Sync Type'),
                'required' => true,
                'values' => array(
                    'full' => 'Full Sync - No From Date',
                    'partial' => 'Partial Sync - With From Date'
                ),
                'name' => 'sync_type'
            )
        );        
        
        $after = '<small>YYYY-MM-DD</small><br /><small>To Update Date Click on Calendar Icon</small>';
        $synValues = mage::getStoreConfig('epicor_comm_enabled_messages/syn_request/full_sync');
        if (Mage::getStoreConfig('Epicor_Comm/licensing/erp') != '') {
            if($synValues){
                $msgArray = explode(',', $synValues);
                $endMsg = '';
                if(count($msgArray) > 1){
                    $lastMsg = array_pop($msgArray);
                    $endMsg = " and ".$lastMsg;
                }
                $flatMsg = implode(',', $msgArray);               
                $after .= "<br /><strong>Note:</strong> Date From will be ignored for {$flatMsg}{$endMsg} </br>messages"; 
            }    
        }
        
        $fromDate = $fieldset->addField(
            'date', 
            'date', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Date From'),
                'comment' => 'Change Date Using Date Picker',
                'tabindex' => 1,
                'class' => 'validate-date',
                'required' => false,
                'name' => 'date',
                'readonly' => true,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => 'yyyy-MM-dd',
                'value' => Mage::helper('epicor_comm')->getLocalDate(strtotime('-1 week'), 'yyyy-MM-dd'),
                'after_element_html' => $after,
            )
        );
        $fromTime = $fieldset->addField(
            'time', 
            'time', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Time From'),              
                'tabindex' => 1,
                'class' => 'validate-time',
                'required' => false,
                'name' => 'time',
                'readonly' => true,              
                'format' => 'hh:mm:ss',
//                'value' => Mage::helper('epicor_comm')->getLocalTime()               
                'value' => Mage::getModel('core/date')->date('H:i:s')    
            )
        );

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($fromDate->getHtmlId(), $fromDate->getName())
            ->addFieldMap($syncType->getHtmlId(), $syncType->getName())
            ->addFieldMap($fromTime->getHtmlId(), $fromTime->getName())
            ->addFieldDependence(
                $fromDate->getName(),
                $syncType->getName(),
                'partial'
            )
            ->addFieldDependence(
                $fromTime->getName(),
                $syncType->getName(),
                'partial'
            )
        );
 
        
        return parent::_prepareForm();
    }

    /**
     * Gets an array of upload messages, by checking the relevant directory
     * 
     * @return array - array of messages
     */
    private function _getSimpleUploadMessages()
    {
        $messages = array();
        $simpleMessages = Mage::helper('epicor_comm/messaging')->getSimpleMessageTypes('sync');
        if (!empty($simpleMessages)) {
            foreach ($simpleMessages as $type => $desc) {
                $desc = (array) $desc;
                $msgTypes = implode(',', $desc['value']);    // put codes required for task in csv string
                $messages[] = array(
                    'label' => $desc['label'],
                    'value' => strtoupper($msgTypes),
                );
            }
        }
        return $messages;
    }

    private function _getUploadMessages()
    {
        $messages = array();
        $messageTypes = Mage::helper('epicor_comm/messaging')->getMessageTypes('upload');
        
        $excluded = array('FSUB', 'FREQ');

        if (!empty($messageTypes)) {
            foreach ($messageTypes as $type => $desc) {
                $type = strtoupper($type);
                if(!in_array($type,$excluded)) {
                    $desc = (array) $desc;
                    $messages[] = array(
                        'label' => $desc['label'],
                        'value' => $type,
                    );
                }
            }
        }

        return $messages;
    }

    /**
     * Gets an array of languages by checking each store for it's language
     * 
     * @return array - array of languages
     */
    private function _getLanguages()
    {
        $stores = Mage::app()->getStores();

        $languages = array();
        $locales = Mage::app()->getLocale()->getOptionLocales();

        foreach ($stores as $store) {
            $storeCode = Mage::getStoreConfig('general/locale/code', $store->getId());

            // only add the language if we don't already have it
            if (isset($storeCode) && !isset($languages[$storeCode])) {

                $test = new Zend_Locale($storeCode);

                $languages[$storeCode] = array(
                    'label' => $storeCode,
                    'value' => $storeCode,
                );
            }
        }

        foreach ($locales as $locale) {
            if (isset($languages[$locale['value']])) {
                $languages[$locale['value']]['label'] = $locale['label'];
            }
        }

        return $languages;
    }
}
