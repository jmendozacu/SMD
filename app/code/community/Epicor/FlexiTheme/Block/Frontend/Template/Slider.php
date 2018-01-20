<?php
/**
 * 
 * Slider block
 * 
 * setTransitionType($value)
 * setTransitionSpeed($value)
 * setSlideDuration($value)
 * setShowControls($value)
 * setRandomOrder($value)
 * setSlides($value)
 * setJavaScriptId($value)
 * getTransitionType()
 * getTransitionSpeed()
 * getSlideDuration()
 * getShowControls()
 * getRandomOrder()
 * getSlides()
 * getJavaScriptId()
 * 
 */
class Epicor_FlexiTheme_Block_Frontend_Template_Slider extends Mage_Catalog_Block_Product_Abstract {
    /*
      $this->setTransitionType"><value>cover</value></action>
      $this->setTransitionSpeed"><value>1000</value></action>
      $this->setSlideDuration"><value>3000</value></action>
      $this->setShowControls"><value>0</value></action>
      $this->setRandomOrder"><value>1</value></action>
      $this->setSlides"><value>20,21,33,34</value></action>
     */

    public function generateSlideContent($templateType = '') {
        $slides = array();
        $template = '';

        foreach ($this->getSlides() as $callout_id) {
            $callout = Mage::getModel('flexitheme/layout_block')->load($callout_id);
            if ($callout->getId()) {
                $block = $this->getLayout()->createBlock($callout->getBlockType());
                /* @var $block Mage_Core_Block_Abstract */
                switch ($templateType) {
                    case 'side':
                        $template = ($callout->getBlockTemplateLeft()) ? $callout->getBlockTemplateLeft() : $callout->getBlockTemplateRight();
                        break;
                    case 'top':
                        $template = ($callout->getBlockTemplateHeader()) ? $callout->getBlockTemplateHeader() : $callout->getBlockTemplateFooter();
                        break;
                    case 'right':
                        $template = ($callout->getBlockTemplateRight()) ? $callout->getBlockTemplateRight() : $callout->getBlockTemplateLeft();
                        break;
                    case 'footer':
                        $template = ($callout->getBlockTemplateFooter()) ? $callout->getBlockTemplateFooter() : $callout->getBlockTemplateHeader();
                        break;
                }
                if (empty($template))
                    $template = $callout->getBlockTemplate();

                $block->setTemplate($template);
                $block_xml = '<root>' . $callout->getBlockXml() . '</root>';
                $xmlDoc = Mage::helper('flexitheme')->xmlToArray($block_xml);
                
                $method = $xmlDoc['root']['action']['attr']['method'];
                $value = $xmlDoc['root']['action']['block_id']['value'];
                
                $block->$method($value);
                
                $html = $block->toHtml();
                $html_tmp = trim($html);
                if (!empty($html_tmp))
                    $slides[] = $html;
            }
        }

        return $slides;
    }

    /**
     * Loads the block model and sets up variables required
     * 
     * @param integer $block_id 
     * 
     */
    public function setBlockId($block_id) {

        $block = Mage::getModel('flexitheme/layout_block')->load(base64_decode($block_id), 'block_name');

        if ($block) {
            $data = unserialize($block->getBlockExtra());

            $this->setModelId($block->getId());
            $this->setTransitionType($data['transition']);
            $this->setTransitionSpeed($data['transition_time']);
            $this->setSlideDuration($data['display_time']);
            $this->setShowControls(($data['paging'] == 1 ? true : false));
            $this->setRandomOrder($data['random']);
            $this->setSlides($data['sections']);
            $this->setJavaScriptId(Mage::helper('flexitheme/layout')->safeString($data['name'], ''));
        }
    }
}
