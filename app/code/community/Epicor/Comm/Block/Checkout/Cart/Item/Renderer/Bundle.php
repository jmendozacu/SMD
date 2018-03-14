<?php
/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Epicor_Comm_Block_Checkout_Cart_Item_Renderer_Bundle extends Mage_Bundle_Block_Checkout_Cart_Item_Renderer
{
    public function getFormatedOptionValue($optionValue)
    {
        $result = parent::getFormatedOptionValue($optionValue);
        
        $result['value'] = preg_replace('/<span class="price">.*<\/span>/', '', $result['value']);
        
        return $result;
    }
    
    public function isGroupProduct($product){
        if($product->getTypeId() == "bundle"){
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            if($parentIds){
                $parentProduct = Mage::getModel('catalog/product')->load($parentIds);
                return $parentIds[0];
            }
        }
    }
    
    public function getProductUrlPath($id){
        $product = Mage::getModel('catalog/product')->load($id);
        return $product->getUrlPath();
        
    }
    
        /**
     * Return cart item error messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }


        // Add messages saved previously in checkout session
        $checkoutSession = $this->getCheckoutSession();
        if ($checkoutSession) {
            /* @var $collection Mage_Core_Model_Message_Collection */
            $collection = $checkoutSession->getQuoteItemMessages($quoteItem->getId(), true);
            if ($collection) {
                $additionalMessages = $collection->getItems();
                foreach ($additionalMessages as $message) {
                    /* @var $message Mage_Core_Model_Message_Abstract */
                    $messages[] = array(
                        'text' => $message->getCode(),
                        'type' => ($message->getType() == Mage_Core_Model_Message::ERROR) ? 'error' : 'notice'
                    );
                }
            }
        }

        return $messages;
    }
}
