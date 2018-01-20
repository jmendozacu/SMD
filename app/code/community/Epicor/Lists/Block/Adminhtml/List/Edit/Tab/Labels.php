<?php

/**
 * List Labels Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Labels extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Labels';
    }

    /**
     * Builds List Labels Form
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Labels
     */
    protected function _prepareForm()
    {
        $list = Mage::registry('list');
        /* @var $list Epicor_Lists_Model_List */

        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (empty($formData)) {
            $formData = $list->getData();
        }

        $fieldset = $form->addFieldset('default', array('legend' => $this->__('Default Label')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $fieldset->addField(
            'label', 'text', array(
            'label' => $this->__('Default'),
            'required' => false,
            'name' => 'label',
            'disabled' => true,
            'note' => $this->__('Default label for this list')
            )
        );

        $this->addWebsites($form);

        $labels = $list->getLabels();
        $sortedLabels = $list->getSortedLabels();

        foreach ($sortedLabels as $type => $ids) {
            foreach ($ids as $typeId => $labelId) {
                $label = $labels[$labelId];
                /* @var $label Epicor_Lists_Model_List_Label */
                $formData['label_' . $type . '_' . $typeId] = $label->getLabel();
            }
        }

        $form->setValues($formData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Adds Website fields to the form
     *
     * @param Varien_Data_Form $form
     * @param Epicor_Lists_Model_List $list
     *
     * @return void
     */
    protected function addWebsites($form)
    {
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        foreach ($storeModel->getWebsiteCollection() as $website) {
            /* @var $website Mage_Core_Model_Website */

            $groups = $website->getGroupCollection();
            if ($groups->count() > 0) {
                $this->addStoreGroupFields($form, $website, $groups->getItems());
            }
        }
    }

    /**
     * Adds Store Group specific fields to the form
     *
     * @param Varien_Data_Form $form
     * @param Epicor_Lists_Model_List $list
     * @param Mage_Core_Model_Website $website
     * @param array $groups
     *
     * @return void
     */
    protected function addStoreGroupFields($form, $website, $groups)
    {
        $fieldset = $form->addFieldset('website_' . $website->getId(), array('legend' => $website->getName()));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset->addType('heading', 'Epicor_Common_Lib_Varien_Data_Form_Element_Heading');

        $webNameBase = 'labels[' . $website->getId() . ']';
        $fieldName = $webNameBase . '[default]';

        $this->addLabelField(
            $fieldset, 'websites', $website->getId(), $fieldName, $this->__('Default for Website')
        );

        foreach ($groups as $group) {

            $groupNameBase = $webNameBase . '[groups][' . $group->getId() . ']';

            /* @var $group Mage_Core_Model_Store_Group */
            $this->addGroupHeading(
                $fieldset, $group->getId(), $group->getName()
            );

            $collection = $group->getStoreCollection();
            if ($collection->count() > 1) {
                $fieldName = $groupNameBase . '[default]';
                $this->addLabelField(
                    $fieldset, 'store_groups', $group->getId(), $fieldName, $this->__('Default for Store')
                );
                $this->addStoreViewHeading($fieldset, $group->getId());
            }

            foreach ($collection->getItems() as $store) {
                /* @var $store Mage_Core_Model_Store */
                $fieldName = $groupNameBase . '[stores][' . $store->getId() . ']';
                $this->addLabelField(
                    $fieldset, 'stores', $store->getId(), $fieldName, $store->getName()
                );
            }
        }
    }

    /**
     * Adds a label field to the display
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $type
     * @param integer $id
     * @param string $fieldName
     * @param string $label
     */
    protected function addLabelField($fieldset, $type, $id, $fieldName, $label)
    {
        $fieldset->addField(
            'label_' . $type . '_' . $id, 'text', array(
            'label' => $label,
            'name' => $fieldName,
            )
        );
    }

    /**
     * Adds a label field to the display
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param integer $id
     * @param string $name
     */
    protected function addGroupHeading($fieldset, $id, $name)
    {
        $fieldset->addField(
            'group_heading_' . $id, 'heading', array(
            'label' => $this->__('Store: ') . $name
        ));
    }

    /**
     * Adds a label field to the display
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param integer $id
     */
    protected function addStoreViewHeading($fieldset, $id)
    {
        $fieldset->addField(
            'storeview_heading_' . $id, 'heading', array(
            'label' => $this->__('Store Views'),
            'subheading' => true
        ));
    }

}
