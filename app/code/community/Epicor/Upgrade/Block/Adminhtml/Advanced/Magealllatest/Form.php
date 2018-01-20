<?php

/**
 * Epicor_Upgrade_Block_Adminhtml_Advanced_Cleardata
 * 
 * Form for Tar Patch
 * 
 * @category   Epicor
 * @package    Epicor_Upgrade
 * @author     Epicor Websales Team
 */
class Epicor_Upgrade_Block_Adminhtml_Advanced_Magealllatest_Form extends Mage_Adminhtml_Block_Widget_Form
{

    const PATCHED = 'Mage All Latest file is Patched Correctly. No Action Required';
    const UNPATCHED = 'Mage All Latest file is unpatched. Please press "Patch File" above to patch the file';

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'patch_form',
            'action' => $this->getUrl('*/*/patch'),
            'method' => 'post'
        ));

        $this->setForm($form);

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('patch_form', array('legend' => Mage::helper('epicor_upgrade')->__('Patch Info')));

        $label = (Mage::registry('mage_all_latest_file_exist')) ? self::PATCHED : self::UNPATCHED;

        if (!Mage::registry('mage_all_latest_file_exist')) {
            $fieldset->addField('title', 'note', array(
                'text' => Mage::helper('epicor_upgrade')->__('The Magento package downloaded via Magento Connect has been missing a file since 1.9.2.0. as such is is stopping upgrading unless the missing file is created.'),
            ));
        }

        $fieldset->addField('note', 'note', array(
            'text' => Mage::helper('epicor_upgrade')->__($label),
        ));
        return parent::_prepareForm();
    }

}
