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

class Blackbird_ContentManager_Block_Og extends Mage_Core_Block_Abstract
{
    
    /**
     * Render block HTML
     * Add meta tags to the <head>
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if($this->getOgTitle()) $html .= '<meta property="og:title" content="'.$this->getOgTitle().'"/>'."\n";
        if($this->getOgDescription()) $html .= '<meta property="og:description" content="'.$this->getOgDescription().'"/>'."\n";
        if($this->getOgUrl()) $html .= '<meta property="og:url" content="'.$this->getOgUrl().'"/>'."\n";
        if($this->getOgType()) $html .= '<meta property="og:type" content="'.$this->getOgType().'"/>'."\n";
        if($this->getOgImage()) $html .= '<meta property="og:image" content="'.$this->getOgImage().'"/>'."\n";
        
        return $html;
    }
    
}