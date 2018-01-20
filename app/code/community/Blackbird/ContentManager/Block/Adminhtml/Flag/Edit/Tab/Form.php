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
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Flag_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contenttype_form', array('legend'=>Mage::helper('contentmanager')->__('Assign flags for each store')));
       
        $stores = Mage::getModel('core/store')->getCollection();
        
        foreach($stores as $store)
        {
            $fieldset->addField('store_'.$store->getId(), 'select', array(
                'label'     => $store->getWebsite()->getName() . ' - ' . $store->getGroup()->getName() . ' - ' . $store->getName(),
                'name'      => 'store_'.$store->getId(),
                'values'    => $this->getAvailableIcons(),
                'class'     => 'f-right',
                'after_element_html'    => '<div class="flag f-left"></div><script type="text/javascript">jQuery(\'#store_'.$store->getId().'\').parent().children(\'.flag\').html(\'<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'contenttype/flags/\'+jQuery(\'#store_'.$store->getId().'\').val()+\''.'" />\'); jQuery(\'#store_'.$store->getId().'\').change(function() { jQuery(this).parent().children(\'.flag\').html(\'<img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'contenttype/flags/\'+this.value+\''.'" />\') });</script>'
            ));
        }

        if ( Mage::getSingleton('adminhtml/session')->getFlagData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFlagData());
            Mage::getSingleton('adminhtml/session')->setFlagData(null);
        } else {
            $flags = Mage::getModel('contentmanager/flag')->getCollection();
            $data = array();
            foreach($flags as $flag)
            {
                $data['store_'.$flag->getStoreId()] = $flag->getValue();
            }
            $form->setValues($data);
        }
        return parent::_prepareForm();
    }
    
    public function getAvailableIcons()
    {
        $matches = glob(Mage::getBaseDir('media').DS.'contenttype/flags'.DS.'*.png');
        $result = array();
        $result[''] = Mage::helper('contentmanager')->__('Please select');
        foreach($matches as $match)
        {
            $result[substr( $match, ( strrpos( $match, DS ) +1 ) )] = substr( $match, ( strrpos( $match, DS ) +1 ) );
        }
        
        return $result;
    }
    
}