<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Api_Soap_Header extends SoapHeader
{
    private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

    function __construct($params)
    {

        $user = $params['username'];
        if(empty($user)){
            throw new Mage_Exception('Username not provided');
        }

        $pass = $params['password'];
        if(empty($pass)){
            throw new Mage_Exception('Password not provided');
        }

        $ns = $params['ns'];
        if ($ns) {
            $this->wss_ns = $ns;
        }
        $auth                          = new stdClass();
        $auth->Username                = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Password                = new SoapVar($pass, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $username_token                = new stdClass();
        $username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);
        $security_sv                   = new SoapVar(
            new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
            SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);

        parent::__construct($this->wss_ns, 'Security', $security_sv, true);
    }
}