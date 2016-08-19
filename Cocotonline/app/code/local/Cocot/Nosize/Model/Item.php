<?php

class Cocot_Nosize_Model_Item extends Mage_Core_Model_Abstract 
{

    protected function _construct() 
    {
        $this->_init('nosize/item', 'nosize_id');
    }
    
    public function sendEmail() {
       
        Mage::app()->setCurrentStore(Mage::app()->getDefaultStoreView());
        $collection = $this->getCollection()->getNoSent();
        
        foreach ($collection as $item) {
            
            $_product = Mage::getModel('catalog/product')->load($item['simple_id']);

            if ($_product->isSalable()) {                
                $_image = Mage::helper('catalog/image')
                      ->init( Mage::getModel('catalog/product')->load($item['product_id'] ), 'image')
                      ->resize(326, 490);
            
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('nosize');
                $emailTemplateVariables = array(
                    'customer_name' => $item['name'],
                    'product_url' => $item['product_url'],
                    'product_name' => $item['product_name'],
                    //'product_brand' => $item['product_brand'],
                    'product_size' => $item['size'],
                    'product_image' => $_image
                );
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                $emailTemplate->setSenderName('Cocot');
                $emailTemplate->setSenderEmail('atencionalcliente@cocotonline.com.ar');
                if ( $emailTemplate->send($item['email'], $item['name'], $emailTemplateVariables) ) {
                    $item->setSent(true);
                    $item->setSentDate(date('Y-m-d H:i:s'));
                    $item->save();
                }
            }
        }
    }

}

