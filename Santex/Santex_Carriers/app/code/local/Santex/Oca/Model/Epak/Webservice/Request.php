<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Epak_Webservice_Request extends Varien_Object
{
    protected $wdsl = '';

    /**
     * Makes the call to the epak WS
     * @param  String  $method The method to call
     * @param  Array   $params Params(optional)
     * @return Santex_Oca_Model_Epak_Webservice_Response
     */
    protected function makeSoapCall($method, $params=array(), $useCache=true) {
        if ($useCache) {
            $cache = Mage::getModel('oca/epak_webservice_cache');
            $callKey = $method.'_'.md5(implode('', $params));
            $response_str = $cache->getData($callKey);
        } else {
            $response_str = false;
        }
        
        if (!$response_str) {
            Mage::log('OCA call from WS. Key: '.$callKey);
            $this->getEpakWdsl();
            $client = new SoapClient($this->wdsl);
            $response = (array)$client->$method($params);
            //Lo que nos interesa EN TODOS LOS responses, es el campo "any"
            $response_root = array_pop($response);
            $response_str = isset ($response_root->any) ? $response_root->any : '';
            if ($useCache) $cache->setData($callKey, $response_str);
        } else {
            Mage::log('OCA call from CACHE. Key: '.$callKey);
        }
        return Mage::getModel('oca/epak_webservice_response')->setResponseString($response_str);
    }

    /**
     * Gets the WS URL From config
     * @return string URL of the WS
     */
    protected function getEpakWdsl() {
        if($this->wdsl == '') {
            $this->wdsl = Mage::getStoreConfig('oca/settings/ws_url');
        }
        return $this->wdsl;
    }
}
