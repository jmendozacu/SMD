<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 09/06/2016
 * Time: 09:56
 */
class Webtise_Gallery_Model_Observer
{
    /**
     * Initialise Banners on the page if applicable
     *
     * @param $observer
     */
    public function initBanners($observer) {
        /** @var Mage_Core_Model_Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $galleries = Mage::helper('gallery')->getGalleryCollection();
        if($galleries) {
            foreach ($galleries as $gallery) {
                $display = $gallery->getDisplayType();
                $block = $layout->createBlock(
                    'Webtise_Gallery_Block_View',
                    'gallery.view.'.$gallery->getId(),
                    array(
                        'template'  => 'webtise/gallery/view/'.$display.'.phtml'
                    )
                );
                $block->setData('gallery_id', $gallery->getId());
                $blockref = $gallery->getBlockReference();
                if($ref = $layout->getBlock($blockref)) {
                    $ref->append($block);
                }else {
                    Mage::helper('gallery')->logException('Reference block form gallery Id'. $gallery->getId() .'was not present in layout.');
                }
            }
            if(Mage::app()->getRequest()->getRouteName() === 'cms') {
                $page = Mage::getSingleton('cms/page');
                if($page->getId() && $page->getShowTagNavigation()) {
                    if($left = $layout->getBlock('left')) {
                        $query = Mage::app()->getRequest()->getQuery();
                        $left->unsetChild('cms_menu');
                        $left->append(
                            $layout->createBlock(
                                'Webtise_Gallery_Block_View_Tag_Navigation',
                                'gallery.tag.navigation',
                                array(
                                    'galleries' => $galleries,
                                    'query'     => $query
                                )
                            )
                        );
                    }
                }
            }
        }
    }

    public function initCmsFields($observer)
    {
        $page = Mage::registry('cms_page');
        $form = $observer->getForm();
        $fieldset = $form->addFieldset(
            'gallery_fieldset',
            array(
                'legend'=>Mage::helper('cms')->__('Gallery Properties'),
                'class'=>'fieldset-wide'
            )
        );

        $fieldset->addField('is_gallery_page', 'select', array(
            'name'                  => 'is_gallery_page',
            'index'                 => 'is_gallery_page',
            'label'                 => Mage::helper('cms')->__('Gallery Page'),
            'title'                 => Mage::helper('cms')->__('Gallery Page'),
            'disabled'              => false,
            'values'                => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
            'after_element_html'    => '<p class="nm"><small>Is this page being used as a gallery in conjunction with the Webtise Gallery extension?</small></p>'
        ));

        $fieldset->addField('show_tag_navigation', 'select', array(
            'name'                  => 'show_tag_navigation',
            'index'                 => 'show_tag_navigation',
            'label'                 => Mage::helper('cms')->__('Show Tag Navigation'),
            'title'                 => Mage::helper('cms')->__('Show Tag Navigation'),
            'disabled'              => false,
            'values'                => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
            'after_element_html'    => '<p class="nm"><small>Replace Left Sidebar with Tag layered navigation?</small></p>'
        ));

        $formValues = $page->getData();
        $form->addValues($formValues);
    }
}