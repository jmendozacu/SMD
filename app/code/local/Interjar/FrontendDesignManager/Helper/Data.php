<?php
/**
 * @package Interjar_FrontendDesignManager
 * @author Interjar Ltd
 * @author Andy Burns <andy@interjar.com>
 */
class Interjar_FrontendDesignManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ENABLE_DESIGN_MANAGER_MODULE = 'frontenddesignmanager/general/enabled_designmanager';

    const HEADER_DESIGN_OVERRIDE_ENABLED = 'frontenddesignmanager/header_section/header_enabled';

    const HEADER_DESIGN_OVERRIDE_BACKGROUND = 'frontenddesignmanager/header_design/header_background';
    const HEADER_DESIGN_OVERRIDE_TOP_LINKS_COLOUR = 'frontenddesignmanager/header_design/header_top_links';

    const HEADER_ACCOUNT_MENU_BORDER_COLOUR = 'frontenddesignmanager/header_account_method/account_border_colour';
    const HEADER_ACCOUNT_MENU_BACKGROUND_COLOUR = 'frontenddesignmanager/header_account_method/account_background_colour';
    const HEADER_ACCOUNT_MENU_LINK_FONT_COLOUR = 'frontenddesignmanager/header_account_method/account_link_font_colour';
    const HEADER_ACCOUNT_MENU_LINK_FONT_COLOUR_HOVER = 'frontenddesignmanager/header_account_method/account_link_font_colour_hover';

    const MENU_DESIGN_OVERRIDE_ENABLED = 'frontenddesignmanager/menu_section/menu_enabled';
    const MENU_DESIGN_OVERRIDE_FONT_COLOUR = 'frontenddesignmanager/menu_section/menu_font_colour';
    const MENU_DESIGN_OVERRIDE_FONT_COLOUR_HOVER = 'frontenddesignmanager/menu_section/menu_font_colour_hover';
    const MENU_DESIGN_OVERRIDE_SUB_BACKGROUND = 'frontenddesignmanager/menu_section/menusub_background';
    const MENU_DESIGN_OVERRIDE_SUB_BACKGROUND_HOVER = 'frontenddesignmanager/menu_section/menusub_background_hover';

    const CHECKOUT_DESIGN_OVERRIDE_ENABLED = 'frontenddesignmanager/checkout_section/checkout_enabled';
    const CHECKOUT_DESIGN_OVERRIDE_SECTIONS_BACKGROUND = 'frontenddesignmanager/checkout_section/checkout_sections_background';
    const CHECKOUT_DESIGN_OVERRIDE_SECTIONS_FONT_COLOUR = 'frontenddesignmanager/checkout_section/checkout_sections_font_background';

    const FOOTER_DESIGN_OVERRIDE_ENABLED = 'frontenddesignmanager/footer_section/footer_enabled';
    const FOOTER_DESIGN_OVERRIDE_FOOTER_BACKGROUND = 'frontenddesignmanager/footer_section/footer_background';
    const FOOTER_DESIGN_OVERRIDE_FONT_COLOUR = 'frontenddesignmanager/footer_section/footer_font_colour';

    /**
     * @var
     */
    private $storeId;

    /**
     * Interjar_FrontendDesignManager_Helper_Data constructor.
     */
    public function __construct()
    {
        $this->storeId = Mage::app()->getStore()->getId();
    }

    /**
     * Get module enabled?
     *
     * @return bool
     */
    public function getOverridesEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(
            self::ENABLE_DESIGN_MANAGER_MODULE,
            $this->storeId
        );
    }

    /**
     * Get store header overrides enabled?
     *
     * @return bool
     */
    public function getHeaderOverridesEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(
            self::HEADER_DESIGN_OVERRIDE_ENABLED,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getHeaderBackground()
    {
        return Mage::getStoreConfig(
            self::HEADER_DESIGN_OVERRIDE_BACKGROUND,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getTopLinksColour()
    {
        return Mage::getStoreConfig(
            self::HEADER_DESIGN_OVERRIDE_TOP_LINKS_COLOUR,
            $this->storeId
        );
    }

    /**
     * Get store menu overrides enabled?
     *
     * @return bool
     */
    public function getMenuOverridesEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(
            self::MENU_DESIGN_OVERRIDE_ENABLED,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getMenuFontColour()
    {
        return Mage::getStoreConfig(
            self::MENU_DESIGN_OVERRIDE_FONT_COLOUR,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getMenuFontColourHover()
    {
        return Mage::getStoreConfig(
            self::MENU_DESIGN_OVERRIDE_FONT_COLOUR_HOVER,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getMenuSubBackgroundColour()
    {
        return Mage::getStoreConfig(
            self::MENU_DESIGN_OVERRIDE_SUB_BACKGROUND,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getMenuSubBackgroundColourHover()
    {
        return Mage::getStoreConfig(
            self::MENU_DESIGN_OVERRIDE_SUB_BACKGROUND_HOVER,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAccountMenuBorderColour()
    {
        return Mage::getStoreConfig(
            self::HEADER_ACCOUNT_MENU_BORDER_COLOUR,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAccountMenuBackgroundColour()
    {
        return Mage::getStoreConfig(
            self::HEADER_ACCOUNT_MENU_BACKGROUND_COLOUR,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAccountMenuLinkFontColour()
    {
        return Mage::getStoreConfig(
            self::HEADER_ACCOUNT_MENU_LINK_FONT_COLOUR,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getAccountMenuLinkFontColourHover()
    {
        return Mage::getStoreConfig(
            self::HEADER_ACCOUNT_MENU_LINK_FONT_COLOUR_HOVER,
            $this->storeId
        );
    }

    /**
     * Get store checkout overrides enabled?
     *
     * @return bool
     */
    public function getCheckoutOverridesEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(
            self::CHECKOUT_DESIGN_OVERRIDE_ENABLED,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getCheckoutSectionsBackground()
    {
        return Mage::getStoreConfig(
            self::CHECKOUT_DESIGN_OVERRIDE_SECTIONS_BACKGROUND,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getCheckoutSectionsFontColour()
    {
        return Mage::getStoreConfig(
            self::CHECKOUT_DESIGN_OVERRIDE_SECTIONS_FONT_COLOUR,
            $this->storeId
        );
    }

    /**
     * Get store footer overrides enabled?
     *
     * @return bool
     */
    public function getFooterOverridesEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(
            self::FOOTER_DESIGN_OVERRIDE_ENABLED,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getFooterBackgroundColour()
    {
        return Mage::getStoreConfig(
            self::FOOTER_DESIGN_OVERRIDE_FOOTER_BACKGROUND,
            $this->storeId
        );
    }

    /**
     * @return mixed
     */
    public function getFooterFontColour()
    {
        return Mage::getStoreConfig(
            self::FOOTER_DESIGN_OVERRIDE_FONT_COLOUR,
            $this->storeId
        );
    }
}
