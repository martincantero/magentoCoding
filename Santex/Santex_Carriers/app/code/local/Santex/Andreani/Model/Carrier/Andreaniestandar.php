<?php

/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Carrier_Andreaniestandar extends Santex_Andreani_Model_Carrier_Andreani
{
	protected $_code = 'andreaniestandar';

    /**
     * Arma el precio y la información del servicio "Estandar" de Andreani según el parametro $data
     *
     * @param Datos del usuario y el carrito de compras $data
     * @return Los datos para armar el Método de envío $rate
     */
    protected function _getAndreaniEstandar($datos,$request){
        $helper = Mage::helper('andreani/data');

        $helper->_getLogger()->saveLog("Andreani Estandar", $this->_process_name);

        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle("Andreani");
        $rate->setMethod($this->_code);

        $datos["contrato"]      = Mage::getStoreConfig('carriers/andreaniestandar/contrato',Mage::app()->getStore());

        // Buscamos en eAndreani el costo del envio segun los parametros enviados
        $datos["precio"]                = $this->cotizarEnvio($datos, Santex_Andreani_Helper_Data::WS_TIPO_COTIZAR);
        $datos["CategoriaDistanciaId"]  = $this->envio->CategoriaDistanciaId;
        $datos["CategoriaPeso"]         = $this->envio->CategoriaPeso;

        Mage::getSingleton('core/session')->setAndreaniEstandar($datos);

        if ($datos["precio"] == 0) {
            return $texto  = Mage::helper('andreani')->__("Error en la conexión con Andreani. Por favor chequee los datos ingresados en la información de envio y vuelva a intentar.");
        } else {
            $texto  = Mage::getStoreConfig('carriers/andreaniestandar/description',Mage::app()->getStore()) . " {$this->envio->CategoriaDistancia}.";
        }

        $rate->setMethodTitle($texto);

        if($request->getFreeShipping() == true || $request->getPackageQty() == $this->getFreeBoxes()) {
            $shippingPrice = '0.00';
            // cambiamos el titulo para indicar que el envio es gratis
            $rate->setMethodTitle(Mage::helper('andreani')->__('Envío gratis.'));
        } else {
            $shippingPrice = $this->getFinalPriceWithHandlingFee($datos["precio"]);
        }

        $shippingPrice = $shippingPrice + ($shippingPrice * Mage::getStoreConfig('carriers/andreaniestandar/regla') / 100);

        $rate->setPrice($shippingPrice);
        $rate->setCost($shippingPrice);

        return $rate;
    }
}
?>