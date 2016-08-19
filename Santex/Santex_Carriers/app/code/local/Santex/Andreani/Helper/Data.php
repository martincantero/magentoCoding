<?php
/**
 * Santex_Andreani
 *
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $_process_name = 'santex_andreani';

    const WS_TIPO_COTIZAR = 'cotizar';
    const WS_TIPO_SUCURSAL = 'sucursal';
    const WS_TIPO_TRAZABILIDAD = 'trazabilidad';
    const WS_TIPO_CONFIRMAR = 'confirmar';

    public function getTrackingpopup($tracking) {

        $collection = Mage::getModel('andreani/order')->getCollection()
            ->addFieldToFilter('cod_tracking', $tracking);
        $collection->getSelect()->limit(1);

        if (!$collection) {
            Mage::log("Andreani :: no existe la orden en la tabla andreani_order.");
            return false;
        }

        foreach($collection as $thing) {
            $datos = $thing->getData();
        }

        if (Mage::getStoreConfig('carriers/andreaniconfig/testmode',Mage::app()->getStore()) == 1) {
            $url  = "https://www.e-andreani.com/eAndreaniWSStaging/Service.svc?wsdl";
        } else {
            $url  = "https://www.e-andreani.com/eAndreaniWS/Service.svc?wsdl";
        }

        $datos["username"]	= Mage::getStoreConfig('carriers/andreaniconfig/usuario',Mage::app()->getStore());
        $datos["password"]  = Mage::getStoreConfig('carriers/andreaniconfig/password',Mage::app()->getStore());

        if ($datos["username"] == "" OR $datos["password"] == "") {
            Mage::log("Andreani :: no existe nombre de usuario o contraseña para eAndreani");
            return;
        }

        try {
            $options = array(
                'soap_version'	=> SOAP_1_2,
                'exceptions'	=> 1,
                'trace'			=> 1,
                'style'			=> SOAP_DOCUMENT,
                'encoding'		=> SOAP_LITERAL
            );

            $optRequest["ObtenerTrazabilidad"] = array(
                'Pieza' => array(
                    'NroPieza'		=> '',
                    'NroAndreani'	=> $tracking,
                    'CodigoCliente'	=> $datos['cliente']
                ));

            $client 	= new SoapClient($url, $options);
            $request 	= $client->__soapCall("ObtenerTrazabilidad", $optRequest);

            foreach( $request->Pieza->Envios->Envio->Eventos as $indice => $valor )
            {
                $eventos[$indice]["Fecha"] 		= $valor->Fecha;
                $eventos[$indice]["Estado"] 	= $valor->Estado;
                $eventos[$indice]["Motivo"] 	= $valor->Motivo;
                $eventos[$indice]["Sucursal"] 	= $valor->Sucursal;
            }

            $estadoenvio = array(
                "Nropieza" 					=> 		$request->Pieza->NroPieza,
                "NombreEnvio" 				=> 		$request->Pieza->Envios->Envio->NombreEnvio,
                "Codigotracking" 			=> 		$request->Pieza->Envios->Envio->NroAndreani,
                "FechAlta"					=>		$request->Pieza->Envios->Envio->FechaAlta,
                "Eventos" 					=> 		$eventos
            );

            return $estadoenvio;

        } 	catch (SoapFault $e) {
            Mage::log(print_r($e,true));
        }

    }

    public function getWeight() {
        $peso 	= 11;
        $medida = 1000;

        $cart = Mage::getModel('checkout/cart')->getQuote();
        foreach ($cart->getAllItems() as $item) {
            $datos["cantidad"][] 	= $item->getProduct()->getQty();
            $datos["peso"][] 		= $item->getProduct()->getWeight();
            $datos["name"][]		= $item->getProduct()->getName();

            $datos["total"]		 = ($item->getProduct()->getQty() * $item->getProduct()->getWeight() * $medida) + $datos["total"];

        }

        return $datos;
    }


    /**
     * Devuelve el cliente Soap.
     *
     * @param $params array que tiene que tener username y password
     * @return SoapClient
     */
    public function getSoapClient($params, $tipo, $process_name){

        if (Mage::getStoreConfig($process_name.'/settings/testmode',Mage::app()->getStore()) == 1) {
            $params["urlconfirmar"]      = "https://www.e-andreani.com/CASAStaging/eCommerce/ImposicionRemota.svc?wsdl";
            $params["urlcotizar"]        = 'https://www.e-andreani.com/CasaStaging/eCommerce/CotizacionEnvio.svc?wsdl';
            $params["urlsucursal"]       = 'https://www.e-andreani.com/CasaStaging/ecommerce/ConsultaSucursales.svc?wsdl';
        } else {
            $params["urlconfirmar"]      = "https://www.e-andreani.com/CASAWS/eCommerce/ImposicionRemota.svc?wsdl";
            $params["urlcotizar"]        = 'https://www.e-andreani.com/CASAWS/eCommerce/CotizacionEnvio.svc?wsdl';
            $params["urlsucursal"]       = 'https://www.e-andreani.com/CASAWS/ecommerce/ConsultaSucursales.svc?wsdl';
        }

        $params["username"] = Mage::getStoreConfig('carriers/andreaniconfig/usuario',Mage::app()->getStore());
        $params["password"] = Mage::getStoreConfig('carriers/andreaniconfig/password',Mage::app()->getStore());


        if ($params["username"] == "" OR $params["password"] == "") {
            $this->_getLogger()->saveLog("Andreani :: no existe nombre de usuario o contraseña para eAndreani", $this->_process_name);
        }

        $options = array(
            'soap_version' => SOAP_1_2,
            'exceptions' => true,
            'trace' => 1,
            'wdsl_local_copy' => true
        );
        $wsse_header = Mage::getModel('andreani/api_soap_header', $params);
        $client = new SoapClient($params["url".$tipo], $options);
        $client->__setSoapHeaders(array($wsse_header));

        return $client;
    }

    public function getSoapClientTrazabilidad($params, $tipo, $process_name){

        if (Mage::getStoreConfig($process_name.'/settings/testmode',Mage::app()->getStore()) == 1) {
            $params["urltrazabilidad"]  = 'https://www.e-andreani.com/eAndreaniWSStaging/Service.svc?wsdl';
        } else {
            $params["urltrazabilidad"] = "https://www.e-andreani.com/eAndreaniWS/Service.svc?wsdl";
        }

        $options = array(
            'soap_version' 	=> SOAP_1_2,
            'exceptions'	=> 1,
            'trace' 		=> 1,
            'style' 		=> SOAP_DOCUMENT,
            'encoding'		=> SOAP_LITERAL,
        );

        $optRequest = array();
        $optRequest["ObtenerTrazabilidad"] = array(
            'Pieza' => array(
                'NroPieza'		=> '',
                'NroAndreani'	=> $params['cod_tracking'],
                'CodigoCliente'	=> $params['cliente']
            ));
        $client = new SoapClient($params["url".$tipo], $options);
        $request = $client->__soapCall("ObtenerTrazabilidad", $optRequest);

        return $request;
    }

    public function _getLogger() {
        if(!isset($this->_logger)) {
            $this->_logger = Mage::getModel('logger/logs');
        }
        return $this->_logger;
    }

}
