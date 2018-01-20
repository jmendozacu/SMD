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
class Blackbird_ContentManager_Model_Source_Options_Type {

    const CT_OPTIONS_GROUPS_PATH = 'global/contentmanager/options/custom/groups';

    public function toOptionArray() {
        $groups = array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --'))
        );

        $helper = Mage::helper('contentmanager');

        foreach (Mage::getConfig()->getNode(self::CT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = array();
            $typesPath = self::CT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $labelPath = self::CT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName()
                        . '/label';
                $types[] = array(
                    'label' => $helper->__((string) Mage::getConfig()->getNode($labelPath)),
                    'value' => $type->getName()
                );
            }

            $labelPath = self::CT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            $groups[] = array(
                'label' => $helper->__((string) Mage::getConfig()->getNode($labelPath)),
                'value' => $types
            );
        }

        return $groups;
    }

    public static function toOptionByContentTypeArray() {
        $helper = Mage::helper('contentmanager');
        $contentType = Mage::registry('current_contenttype');
        $result = array(
            '' => $helper->__('No breadcrumb'),
            'title' => $helper->__('Page Title')
        );
        if ($contentType) {
            foreach ($contentType->getContentTypeOptionsCollection() as $option) {
                $result[$option['identifier']] = $option['title'];
            }
        }

        return $result;
    }

    public static function arrayOfContentTypes() {

        $contentTypeCollection = Mage::getModel('contentmanager/contenttype')->getCollection();
        $array = array();
        foreach ($contentTypeCollection as $contentType) {
            $array[$contentType->getCtId()]= $contentType->getTitle();
        }
        return $array;
    }

}
