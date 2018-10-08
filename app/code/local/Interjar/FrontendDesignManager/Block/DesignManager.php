<?php
/**
 * @package Interjar_FrontendDesignManager
 * @author Interjar Ltd
 * @author Andy Burns <andy@interjar.com>
 */
class Interjar_FrontendDesignManager_Block_DesignManager extends Mage_Core_Block_Template
{
    /** @var Interjar_FrontendDesignManager_Helper_Data */
    private $designHelper;

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _beforeToHtml()
    {
        if (!$this->getTemplate()) {
            $this->setTemplate('interjar/frontenddesignmanager/designmanager.phtml');
        }
        return parent::_beforeToHtml();
    }

    /**
     * Construct parent block
     */
    public function _construct()
    {
        parent::_construct();
        /** @var Interjar_FrontendDesignManager_Helper_Data designHelper */
        $this->designHelper = Mage::helper('frontenddesignmanager');
    }

    /**
     * Get design configuration for overrides
     *
     * @return array
     */
    public function getConfiguration()
    {
        return array(
            'header' => array(
                'header_enabled' => $this->designHelper->getHeaderOverridesEnabled(),
                'header_background' => $this->designHelper->getHeaderBackground(),
                'header_top_links' => $this->designHelper->getTopLinksColour(),
                'header_account_menu' => array(
                    'border_colour' => $this->designHelper->getAccountMenuBorderColour(),
                    'background_colour' => $this->designHelper->getAccountMenuBackgroundColour(),
                    'links_colour' => $this->designHelper->getAccountMenuLinkFontColour(),
                    'links_colour_hover' => $this->designHelper->getAccountMenuLinkFontColourHover()
                )
            ),
            'menu' => array(
                'menu_enabled' => $this->designHelper->getMenuOverridesEnabled(),
                'menu_font_colour' => $this->designHelper->getMenuFontColour(),
                'menu_font_colour_hover' => $this->designHelper->getMenuFontColourHover(),
                'menusub_background' => $this->designHelper->getMenuSubBackgroundColour(),
                'menusub_background_hover' => $this->designHelper->getMenuSubBackgroundColourHover()
            ),
            'checkout' =>array(
                'checkout_enabled' => $this->designHelper->getCheckoutOverridesEnabled(),
                'checkout_sections_background' => $this->designHelper->getCheckoutSectionsBackground(),
                'checkout_sections_font_background' => $this->designHelper->getCheckoutSectionsFontColour()
            ),
            'footer' => array(
                'footer_enabled' => $this->designHelper->getFooterOverridesEnabled(),
                'footer_background' => $this->designHelper->getFooterBackgroundColour(),
                'footer_font_colour' => $this->designHelper->getFooterFontColour()
            )
        );
    }
}
