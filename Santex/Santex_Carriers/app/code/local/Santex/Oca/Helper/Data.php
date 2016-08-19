<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCuit() {
        $cuit = Mage::getStoreConfig('shipping/tax/cuit');
        
        if (!$cuit || strlen($cuit) != 11 || preg_match("/[^0-9]/", $cuit)) {
            Mage::log('CUIT must be digits only with a lenght of 11. Please check: '.$cuit);
            return false;
        }
        
        return substr($cuit,0,2) . '-' . substr($cuit,2,8) . '-' . substr($cuit,10);
    }

    public function formatZipcode($zipcode) {
        if (!$zipcode || empty($zipcode)) return false;
        // Remove non-numeric chars
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);
        
        //Make the zipcode EXACTLY 4 chars long
        $len = strlen($zipcode);
        if ($len < 4) {
            $zipcode = str_pad($zipcode, 4, '0', STR_PAD_LEFT);
        }
        if ($len > 4) {
            $zipcode = substr($zipcode, 0, 4);
        }
        
        return $zipcode;
    }

}
