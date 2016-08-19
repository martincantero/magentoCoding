<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */


class Santex_Andreani_Model_Carrier_Andreani extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {


        protected $_process_name = 'santex_andreani';

        protected $_code = '';
        protected $distancia_final_txt  = '';
        protected $duracion_final       = '';
        protected $mode  = '';
        protected $envio = '';

        /** 
        * Recoge las tarifas del método de envío basados ​​en la información que recibe de $request
        * 
        * @param Mage_Shipping_Model_Rate_Request $data 
        * @return Mage_Shipping_Model_Rate_Result 
        */ 

        public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
            $helper = Mage::helper('andreani/data');
            $helper->_getLogger()->saveLog("Recolecta los distintos valores del carrier.", $this->_process_name);

            $datos["peso"]              = 0;
            $datos["valorDeclarado"]    = 0;
            $datos["volumen"]           = 0;
            $datos["DetalleProductos"]  = "";
            $sku                        = "";
            $freeBoxes                  = 0;
            $pesoMaximo = Mage::getStoreConfig($this->_process_name.'/settings/pesomax',Mage::app()->getStore());

            Mage::getSingleton('core/session')->unsAndreani();

            // Reiniciar variable Sucursales para descachear las Sucursales.
            if(!Mage::getStoreConfig($this->_process_name.'/settings/cache',Mage::app()->getStore())) {
                Mage::getSingleton('core/session')->unsSucursales();
            }

            // Tomamos el attr "medida" segun la configuracion del cliente
            if (Mage::getStoreConfig($this->_process_name.'/settings/medida',Mage::app()->getStore())=="") {
                $datos["medida"] = "gramos";
            } else {
                $datos["medida"] = Mage::getStoreConfig($this->_process_name.'/settings/medida',Mage::app()->getStore());
            }

            if ($datos["medida"]=="kilos") {
                $datos["medida"] = 1000;
            } elseif ($datos["medida"]=="gramos") {
                $datos["medida"] = 1;
            } else {
                $datos["medida"] = 1; //si está vacio: "gramos"
            }
            
            foreach ($request->getAllItems() as $_item) {
                if($sku != $_item->getSku()) {
                    $sku                     = $_item->getSku();
                    $price           = floor($_item->getPrice());
                    $datos["peso"]           = ($_item->getQty() * $_item->getWeight() * $datos["medida"]) + $datos["peso"];
                    $datos["valorDeclarado"] = ($_item->getQty() * $price) + $datos["valorDeclarado"];
                    
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $_item->getSku(), array('volumen'));
                    $datos["volumen"] += ($_item->getQty() * $product->getVolumen() * $datos["medida"]);

                    // Creamos un string con el detalle de cada producto
                    $datos["DetalleProductos"] = "(" . $_item->getQty() . ") " .$_item->getName() . " + " . $datos["DetalleProductos"];

                    // Si la condicion de free shipping está seteada en el producto
                    if ($_item->getFreeShippingDiscount() && !$_item->getProduct()->isVirtual()) {
                        $helper->_getLogger()->saveLog("getFreeShippingDiscount: " . print_r($_item->getQty(),true), $this->_process_name);
                        $freeBoxes += $_item->getQty();
                    }
                }
            }

            // Seteamos las reglas
            if(isset($freeBoxes))   $this->setFreeBoxes($freeBoxes);
            
            $cart   = Mage::getSingleton('checkout/cart');
            $quote  = $cart->getQuote();
            $shippingAddress        = $quote->getShippingAddress();
            $datos["cpDestino"]     = intval($request->getDestPostcode());
            $datos["localidad"]     = $request->getDestCity();
            $datos["provincia"]     = $request->getDestRegionCode();
            $datos["direccion"]     = $request->getDestStreet();
            $datos["nombre"]        = $shippingAddress->getData('firstname');
            $datos["apellido"]      = $shippingAddress->getData('lastname');
            $datos["telefono"]      = $shippingAddress->getData('telephone');
            $datos["email"]         = $shippingAddress->getData('email');

            $datos["username"]      = Mage::getStoreConfig($this->_process_name.'/settings/usuario',Mage::app()->getStore());
            $datos["password"]      = Mage::getStoreConfig($this->_process_name.'/settings/password',Mage::app()->getStore());
            $datos["cliente"]       = Mage::getStoreConfig($this->_process_name.'/settings/nrocliente',Mage::app()->getStore());
            $datos["contrato"]      = Mage::getStoreConfig('carriers/'.$this->_code.'/contrato',Mage::app()->getStore());

            $result = Mage::getModel('shipping/rate_result');
            $method = Mage::getModel('shipping/rate_result_method');

            $error_msg = Mage::helper('andreani')->__("Completá los datos para poder calcular el costo de su pedido.");

            // Optimizacion con OneStepCheckout
            if ($datos["cpDestino"]=="" && $datos["localidad"]=="" && $datos["provincia"]=="" && $datos["direccion"]=="") {
                $error = Mage::getModel('shipping/rate_result_error'); 
                $error->setCarrier($this->_code); 
                $error->setCarrierTitle($this->getConfigData('title')); 
                $error->setErrorMessage($error_msg); 
                return $error;
            }

            $error_msg = Mage::helper('andreani')->__("Su pedido supera el peso máximo permitido por Andreani. Por favor divida su orden en más pedidos o consulte al administrador de la tienda. Gracias y disculpe las molestias.");

            if ($this->_code == "andreaniestandar" & Mage::getStoreConfig('carriers/andreaniestandar/active',Mage::app()->getStore()) == 1) {
                if($datos["peso"] >= $pesoMaximo){
                    $error = Mage::getModel('shipping/rate_result_error'); 
                    $error->setCarrier($this->_code); 
                    $error->setCarrierTitle($this->getConfigData('title')); 
                    $error->setErrorMessage($error_msg); 
                    return $error;
                } else {
                    $response = $this->_getAndreaniEstandar($datos,$request);
                    if(is_string($response)){
                        $error = Mage::getModel('shipping/rate_result_error'); 
                        $error->setCarrier($this->_code); 
                        $error->setCarrierTitle($this->getConfigData('title'));
                        $error->setErrorMessage($response);
                        return $error;
                    } else {
                        $result->append($response);
                    }
                }
            }
            if ($this->_code == "andreaniurgente" & Mage::getStoreConfig('carriers/andreaniurgente/active',Mage::app()->getStore()) == 1) {
                if($datos["peso"] >= $pesoMaximo){
                    $error = Mage::getModel('shipping/rate_result_error'); 
                    $error->setCarrier($this->_code); 
                    $error->setCarrierTitle($this->getConfigData('title')); 
                    $error->setErrorMessage($error_msg); 
                    return $error;
                } else {
                    $response = $this->_getAndreaniUrgente($datos,$request);
                    if(is_string($response)){
                        $error = Mage::getModel('shipping/rate_result_error'); 
                        $error->setCarrier($this->_code); 
                        $error->setCarrierTitle($this->getConfigData('title'));
                        $error->setErrorMessage($response); 
                        return $error;
                    } else {
                        $result->append($response);
                    }
                }
            }
            if ($this->_code == "andreanisucursal" & Mage::getStoreConfig('carriers/andreanisucursal/active',Mage::app()->getStore()) == 1) {
                if($datos["peso"] >= $pesoMaximo){
                    $error = Mage::getModel('shipping/rate_result_error'); 
                    $error->setCarrier($this->_code); 
                    $error->setCarrierTitle($this->getConfigData('title')); 
                    $error->setErrorMessage($error_msg); 
                    return $error;
                } else {
                    $response = $this->_getAndreaniSucursal($datos,$request);
                    if(is_string($response)){
                        $error = Mage::getModel('shipping/rate_result_error'); 
                        $error->setCarrier($this->_code); 
                        $error->setCarrierTitle($this->getConfigData('title')); 
                        $error->setErrorMessage($response); 
                        return $error;
                    } else {
                        $result->append($response);
                    }
                }
            }
 
            return $result;
        }

        /**
         * Get allowed shipping methods
         *
         * @return array
         */
        public function getAllowedMethods() {
            return array($this->_code    => $this->getConfigData('name'));
        }

        /**
         * Cotiza el envio de los productos segun los parametros
         *
         * @param $params 
         * @return $costoEnvio
         */
        public function cotizarEnvio($params, $tipo) {
            try {

                $helper = Mage::helper('andreani/data');
                $client = $helper->getSoapClient($params, $tipo , $this->_process_name);

                $sucursalRetiro     = array('sucursalRetiro' => "");
                $params = array_merge($sucursalRetiro, $params);
                
                $phpresponse = $client->CotizarEnvio(array(
                    'cotizacionEnvio' =>array(
                        'CPDestino' =>$params["cpDestino"],
                        'Cliente'   =>$params["cliente"],
                        'Contrato'  =>$params["contrato"],
                        'Peso'      =>$params["peso"],
                        'SucursalRetiro'=>$params["sucursalRetiro"],
                        'ValorDeclarado'=>$params["valorDeclarado"],
                        'Volumen'   =>$params["volumen"]
                    )));

                $costoEnvio  = floatval($phpresponse->CotizarEnvioResult->Tarifa);
                $this->envio = $phpresponse->CotizarEnvioResult;

                $helper->_getLogger()->saveLog("Cotizar envio: " . print_r($phpresponse->CotizarEnvioResult,true), $this->_process_name);

                return $costoEnvio;

            } catch (SoapFault $e) {
                $helper->_getLogger()->saveLog("Error: " . $e, $this->_process_name);
                //Mage::getSingleton('core/session')->addError('Error en la conexión con eAndreani. Disculpe las molestias.. vuelva a intentar! <br> En caso de persistir el error contacte al administrador de la tienda.');
            }
        }

        /**
         * Trae las sucursales de Andreani segun los parametros
         *
         * @param $params 
         * @return $costoEnvio
         */
        public function consultarSucursales($params,$metodo) {
            $helper = Mage::helper('andreani/data');
            $metodo = Mage::getStoreConfig($this->_process_name.'/settings/metodo',Mage::app()->getStore());
            try {
                // Nos fijamos si ya consultamos la sucursal en Andreani
                if(is_object(Mage::getSingleton('core/session')->getSucursales())) {
                    if($metodo != "sucursal") {
                        $helper->_getLogger()->saveLog("Ya buscó la sucursal en Andreani", $this->_process_name);
                        return Mage::getSingleton('core/session')->getSucursales();
                    } else {
                        //Mage::getSingleton('core/session')->unsGoogleDistance();
                        $helper->_getLogger()->saveLog("Google Distance: " . print_r(Mage::getSingleton('core/session')->getGoogleDistance(),true), $this->_process_name);
                        if(is_object(Mage::getSingleton('core/session')->getGoogleDistance())) {
                            $helper->_getLogger()->saveLog("Ya buscó la sucursal en Google Maps", $this->_process_name);
                            $this->distancia_final_txt = Mage::getSingleton('core/session')->getDistancia();
                            $this->duracion_final      = Mage::getSingleton('core/session')->getDuracion();
                            $this->mode                = Mage::getSingleton('core/session')->getMode();

                            return Mage::getSingleton('core/session')->getGoogleDistance();
                        }
                    }
                }

                $helper = Mage::helper('andreani/data');
                $client = $helper->getSoapClient($params, 'sucursal', $this->_process_name);
                
                $phpresponse = $client->ConsultarSucursales(array(
                    'consulta' => array(
                        'CodigoPostal'  =>  $params["cpDestino"],
                        'Localidad'     =>  NULL,
                        'Provincia'     =>  NULL
                )));

                if (is_object($phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales)) {
                    $helper->_getLogger()->saveLog("Entra si encuentra el CP", $this->_process_name);
                     // Si no tenemos la direccion del cliente pero SI el CP, deberia mostrarnos la sucursal de nuestra localidad sin calcular la distancia a la misma.
                    $sucursales = $phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales;
                    if ($this->_code == "andreanisucursal" && ($metodo == 'medio' || $metodo == 'completo') == 1) { 
                        $sucursales = $this->distancematrix(array('0' => $sucursales),$params["direccion"],$params["localidad"],$params["provincia"]);
                    }
                } else {
                    if($metodo != 'completo'){
                        $sucursales = "nosucursal";                        
                    } else {
                        $phpresponse = $client->ConsultarSucursales(array(
                            'consulta' => array(
                                'CodigoPostal'  =>  NULL,
                                'Localidad'     =>  $params["localidad"],
                                'Provincia'     =>  NULL
                        )));
                        if (is_object($phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales) OR is_array($phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales)) {
                            $helper->_getLogger()->saveLog("Encontro localidad", $this->_process_name);
                            $sucursales = $phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales;
                            if (is_array($sucursales)) {
                                $helper->_getLogger()->saveLog("Encontro mas de una localidad", $this->_process_name);
                                // Consultamos por "localidad" y encontro varios resultados
                                // buscamos en GoogleAPI cual es la sucursal mas cercana segun la direccion del cliente
                                $sucursales = $this->distancematrix($sucursales,$params["direccion"],$params["localidad"],$params["provincia"]); 
                            } else {
                                if ($this->_code == "andreanisucursal") { $sucursales = $this->distancematrix(array('0' => $sucursales),$params["direccion"],$params["localidad"],$params["provincia"]); }
                            }
                        } else {
                            $helper->_getLogger()->saveLog("No encontro la localidad busca por provincia", $this->_process_name);
                            if ($params["provincia"]=="") {
                                $params["provincia"] = NULL;
                                $helper->_getLogger()->saveLog("Entra si la provincia esta vacia", $this->_process_name);
                            }
                            $phpresponse = $client->ConsultarSucursales(array(
                                'consulta' => array(
                                    'CodigoPostal'  =>  NULL,
                                    'Localidad'     =>  NULL,
                                    'Provincia'     =>  $params["provincia"]
                            )));


                            if (is_object($phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales) OR is_array($phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales)) {
                                $helper->_getLogger()->saveLog("Encontro sucursales en la provincia. Si está vacia.. nos trae todas las provincias", $this->_process_name);
                                $sucursales = $phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales;
                                if (is_array($sucursales)) {
                                    $helper->_getLogger()->saveLog("Encontro muchas sucursales en la provincia", $this->_process_name);
                                    // Consultamos por "provincia" y encontro varios resultados
                                    // buscamos en GoogleAPI cual es la sucursal mas cercana segun la direccion del cliente
                                    $sucursales = $this->distancematrix($sucursales,$params["direccion"],$params["localidad"],$params["provincia"]);
                                } else {
                                    if ($this->_code == "andreanisucursal") { $sucursales = $this->distancematrix(array('0' => $sucursales),$params["direccion"],$params["localidad"],$params["provincia"]); }
                                }
                            } else {
                                $helper->_getLogger()->saveLog("No encontro la provincia y busca todas las localidades para determinar la mas cercana", $this->_process_name);
                                // buscar todas las sucursales
                                // buscamos en GoogleAPI cual es la sucursal mas cercana segun la direccion del cliente
                                $phpresponse = $client->ConsultarSucursales(array(
                                    'consulta' => array(
                                        'CodigoPostal'  =>  NULL,
                                        'Localidad'     =>  NULL,
                                        'Provincia'     =>  NULL
                                )));
                                $sucursales = $phpresponse->ConsultarSucursalesResult->ResultadoConsultarSucursales;

                                $sucursales = $this->distancematrix($sucursales,$params["direccion"],$params["localidad"],$params["provincia"]);
                            }
                        }
                    }
                }

                $helper->_getLogger()->saveLog("Sucursal: " . print_r($sucursales, true), $this->_process_name);
                Mage::getSingleton('core/session')->setSucursales($sucursales);

                return $sucursales;

            } catch (SoapFault $e) {
                $helper->_getLogger()->saveLog("Error: " . $e, $this->_process_name);
                //Mage::getSingleton('core/session')->addError('Error en la conexión con eAndreani. Disculpe las molestias.. vuelva a intentar! <br> En caso de persistir el error contacte al administrador de la tienda.');
            }
        }

         /**
         * Determina la menor distancia entre un array de sucursales y la direccion del cliente
         *
         * @param $sucursales,$direccion,$localidad,$provincia
         * @return $sucursales
         */
        public function distancematrix($sucursales,$direccion,$localidad,$provincia) {

            $helper = Mage::helper('andreani/data');

            try {
                $direccion_cliente  = $direccion . "+" . $localidad . "+" .  $provincia;

                $helper->_getLogger()->saveLog("Direccion del cliente: " . $direccion_cliente, $this->_process_name);

                $distancia_final = 100000000;
                $posicion        = "default";
                foreach ($sucursales as $key => $sucursal) {
                    $direccion = explode(',', $sucursal->Direccion);
                    $direccion_sucursal = $direccion[0] . "+" . $direccion[2] . "+" . $direccion[3];

                    $helper->_getLogger()->saveLog("Data: " . print_r($sucursal , true), $this->_process_name);
                    $helper->_getLogger()->saveLog("Sucursal: " . $sucursal->Direccion, $this->_process_name);
                    $helper->_getLogger()->saveLog("Direccion del cliente: " . str_replace(" ","%20",$direccion_cliente), $this->_process_name);
                    $helper->_getLogger()->saveLog("Direccion de sucursal: " . str_replace(" ","%20",$direccion_sucursal), $this->_process_name);

                    $originales     = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
                    $modificadas    = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
                    $direccion_cliente = utf8_decode($direccion_cliente);
                    $direccion_cliente = strtr($direccion_cliente, utf8_decode($originales), $modificadas);
                    $direccion_cliente = strtolower($direccion_cliente);
                    $direccion_cliente = utf8_encode($direccion_cliente);
                    $direccion_sucursal = utf8_decode($direccion_sucursal);
                    $direccion_sucursal = strtr($direccion_sucursal, utf8_decode($originales), $modificadas);
                    $direccion_sucursal = strtolower($direccion_sucursal);
                    $direccion_sucursal = utf8_encode($direccion_sucursal);

                    //$mode = "walking";
                    //$mode = "bicycling";
                    $mode = "driving";
                    $url  = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . str_replace(" ","%20",$direccion_cliente) . "&destinations=" . str_replace(" ","%20",$direccion_sucursal) . "&mode={$mode}&language=es-ES&sensor=false";

                    $api  = file_get_contents($url);
                    $data = json_decode(utf8_encode($api),true);

                    $rows       = $data["rows"][0];
                    $elements   = $rows["elements"][0];

                    $distancia  = $elements["distance"]["value"];
                    $distancia_txt  = $elements["distance"]["text"];
                    $duracion       = $elements["duration"]["text"];
                    
                    if ($distancia_final >= $distancia && !empty($distancia)) {
                        $distancia_final        = $distancia;
                        $distancia_final_txt    = $distancia_txt;
                        $duracion_final         = $duracion;
                        $posicion               = $key;
                    }
                }

                // Desahbiltar método sucursal en el Shipping Method
                if($posicion === "default") {
                    $helper->_getLogger()->saveLog("No se encontro la sucursal.", $this->_process_name);
                    return false;
                }

                $this->distancia_final_txt   = $distancia_final_txt;
                $this->duracion_final        = $duracion_final;
                if($mode=="driving") $this->mode="en auto";

                // Guardamos las variables en session para no tener que volver a llamar a la API de Google
                Mage::getSingleton('core/session')->setGoogleDistance($sucursales[$posicion]);
                Mage::getSingleton('core/session')->setDistancia($distancia_final_txt);
                Mage::getSingleton('core/session')->setDuracion($duracion_final);
                Mage::getSingleton('core/session')->setMode($this->mode);
                return $sucursales[$posicion];

            } catch (SoapFault $e) {
                $helper->_getLogger()->saveLog("Error: " . $e, $this->_process_name);
            }
        }

    }
?>
