<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Epak_Webservice_Cache
{
    const PREFIX = 'oca_';

    public function getData($key) {
        if (Mage::app()->useCache('santex_oca')) {
            return Mage::app()->getCache()->load(self::PREFIX.$key);
        }
        return false;
    }

    public function setData($key, $value) {
        if (Mage::app()->useCache('santex_oca')) {
            $tags = array('OCA_EPAK_CACHE'); //Igual a la que esta en config.xml (global/cache/types/santex_oca/tags) TODO: Ver como cargarla desde ahi (VALE LA PENA?)
            Mage::app()->getCache()->save($value, self::PREFIX.$key, $tags, Mage::getStoreConfig('oca/settings/cache_lifetime'));
        }
        return $this;
    }
}
