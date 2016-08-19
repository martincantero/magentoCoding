<?php
/**
 *
 * Cart product api
 *
 * @category   Fotter
 * @package    Fotter_Apicustomprice
 * @copyright  www.fotter.com.ar
 * @author     Fotter Core Team <martin.cantero@fotter.com.ar>
 * @info:      http://redmine.fotter.net/issues/8116 
 */
class Fotter_Apicustomprice_Model_Cart_Product_Api extends Mage_Checkout_Model_Cart_Product_Api
{
    
            
    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */
    public function updatecustomprice($quoteId, $productsData, $store=null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }
        

        $errors = array();
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, "id");
            } else if (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, "sku");
            } else {
                $errors[] = Mage::helper('checkout')->__("One item of products do not have identifier or sku");
                continue;
            }

            /** @var $quoteItem Mage_Sales_Model_Quote_Item */
            $quoteItem = $this->_getQuoteItemByProduct($quote, $productByItem,
                $this->_getProductRequest($productItem));
            if (is_null($quoteItem->getId())) {
                $errors[] = Mage::helper('checkout')->__("One item of products is not belong any of quote item");
                continue;
            }

            if ($productItem['qty'] > 0) {
                $quoteItem->setQty($productItem['qty']);
            }

            //Setting custom Price
            if(isset($productItem['price'])){
                try {
                    $quoteItem->setCustomPrice($productItem['price']);
                    $quoteItem->setOriginalCustomPrice($productItem['price']);                    
                    $quoteItem->setPrice($productItem['price']);
                    $quoteItem->setBasePrice($productItem['price']);
                    $quoteItem->getProduct()->setIsSuperMode(true);
                    $quoteItem->save();
                } catch(Exception $e) {
                    Mage::log("updatecustomprice - Custom price saving: " . $e->getMessage());
                }            
            }        

        }

        if (!empty($errors)) {
            $this->_fault("update_product_fault", implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
            $quote->save();
        } catch(Exception $e) {
            $this->_fault("update_product_quote_save_fault", $e->getMessage());
        }

        return true;
    }


  
} // Class Mage_Checkout_Model_Cart_Product_Api End
