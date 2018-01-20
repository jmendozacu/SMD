<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit {

    public function getMaxCommentSize() {
        if ($this->limitTextArea()) {
            return Mage::getStoreConfig('checkout/options/max_comment_length');
        }
        return '';
    }

    public function limitTextArea() {
        $result = false;
        if (Mage::getStoreConfigFlag('checkout/options/limit_comment_length')) {
            $value = Mage::getStoreConfig('checkout/options/max_comment_length');
            if (is_numeric($value)) {
                $result = true;
            }
        }
        return $result;
    }

    public function getRemainingCommentSize() {
        $max = $this->getMaxCommentSize();
        $current = $this->getAddress()->getInstructions();
        return $max - strlen($current);
    }

    public function canMarkDefaultShippingBillingAddress(){
        if ($this->getCustomer()->isGuest()) {
            return true;
        }

        return false;
    }

}


