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

class Blackbird_ContentManager_Model_Entity_Attribute_Backend_Datetime extends Mage_Eav_Model_Entity_Attribute_Backend_Datetime
{
    /**
     * Formating date value before save
     *
     * Should set (bool, string) correct type for empty value from html form,
     * neccessary for farther proccess, else date string
     *
     * @param Varien_Object $object
     * @throws Mage_Eav_Exception
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Datetime
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $_formated     = $object->getData($attributeName . '_is_formated');
        
        //we delete a language
        if($object->getData($attributeName) === null)
        {
            return $this;        
        }
        
        if (!$_formated && $object->hasData($attributeName)) {
            try {
                // Blackbird change : adding strtotime() to keep hours data + special cast for european date format
                $value = new Zend_Date($object->getData($attributeName));
                $value = date('Y-m-d g:i A',$value->getTimestamp());
                
            } catch (Exception $e) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid date'));
            }
            
            if (is_null($value)) {
                $value = $object->getData($attributeName);
            }

            $object->setData($attributeName, $value);
            $object->setData($attributeName . '_is_formated', true);
        }

        return $this;
    }
}
