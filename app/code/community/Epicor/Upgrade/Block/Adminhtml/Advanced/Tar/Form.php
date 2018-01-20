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
class Epicor_Upgrade_Block_Adminhtml_Advanced_Tar_Form extends Mage_Adminhtml_Block_Widget_Form
{

    const PATCHED = 'Tar file is Patched Correctly. No Action Required';
    const UNPATCHED = 'Tar file is unpatched. Please press "Patch File" above to patch the file';

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

        $label = (Mage::registry('tar_file_patched')) ? self::PATCHED : self::UNPATCHED;

        if (!Mage::registry('tar_file_patched')) {
            $fieldset->addField('title', 'note', array(
                'text' => Mage::helper('epicor_upgrade')->__('The Magento Downloader Tar.php requires patching so that it does not create folders instead of files when installing ECC / Magento modules.'),
            ));
        }

        $fieldset->addField('note', 'note', array(
            'text' => Mage::helper('epicor_upgrade')->__($label),
        ));
        return parent::_prepareForm();
    }

}
