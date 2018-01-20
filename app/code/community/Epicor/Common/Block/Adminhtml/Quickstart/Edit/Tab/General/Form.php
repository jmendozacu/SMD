<?php

class Epicor_Common_Block_Adminhtml_Quickstart_Edit_Tab_General_Form extends Epicor_Common_Block_Adminhtml_Quickstart_Edit_Tab_Abstract
{
    
    protected function getKeysToRender()
    {
        return array('erp', 'networking', 'licensing');
    }
    
    protected function formExtras($form)
    {
        $versionInfo = Mage::getConfig()->getNode('global/ecc_version_info')->asArray();
        
        $helper = Mage::helper('epicor_common/locale_format_date');
        /* @var $helper Epicor_Common_Helper_Locale_Format_Date */
        
        $fieldset = $form->addFieldset('ecc_version', array('legend' => 'ECC Version', 'class' => 'fieldset-complete'));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        ksort($versionInfo);

        foreach ($versionInfo as $module => $info) {
            if (isset($info['version']) && isset($info['released'])) {

                if (empty($info['released'])) {
                    $text = $info['version'] . ' (Not Released)' ;
                } else {
                    $text = $info['version'] . ' (Released ' . $helper->getLocalFormatDate($info['released'], 'medium', false) . ')' ;
                }

                $fieldset->addField('ecc_version_module_' . $module, 'note', array(
                    'name' => 'ecc_version_module_' . $module,
                    'label' => $module,
                    'text' => $text
                ));
            }
        }
        
        return parent::formExtras($form);
    }

} 