<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
class Wyomind_Attributeoptionpro_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
    public function isUsingStaticUrlsAllowed()
    {
        if (Mage::getSingleton('adminhtml/session')->getStaticUrlsAllowed()) {
            return true;
        }

        return parent::isUsingStaticUrlsAllowed();
    }
}