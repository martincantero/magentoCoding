<?php

/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Carrier_Andreanisucursal extends Santex_Andreani_Model_Carrier_Andreani
{
	protected $_code = 'andreanisucursal';

    /**
     * Arma el precio y la información del servicio "Sucursal" de Andreani según el parametro $data
     *
     * @param Datos del usuario y el carrito de compras $data
     * @return Los datos para armar el Método de envío $rate
     */
    protected function _getAndreaniSucursal($datos,$request){
        $helper = Mage::helper('andreani/data');

        $helper->_getLogger()->saveLog("Andreani Sucursal", $this->_process_name);

        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle("Andreani");
        $rate->setMethod($this->_code);
        $metodo = Mage::getStoreConfig($this->_process_name.'/settings/metodo',Mage::app()->getStore());

        $datos["contrato"]      = Mage::getStoreConfig('carriers/andreanisucursal/contrato',Mage::app()->getStore());

        // Buscamos la sucursal mas cercana del cliente segun el CP ingresado
        $sucursales             = $this->consultarSucursales($datos, Santex_Andreani_Helper_Data::WS_TIPO_SUCURSAL);

        if($sucursales=="nosucursal"){
            return "No hay sucursales cerca de tu domicilio.";
        }elseif ($sucursales->Sucursal == 0) {
            return "Lo siento ha fallado la comunicación con Andreani, por favor vuelve a intentarlo.";
        }

        $datos["sucursalRetiro"]        = $sucursales->Sucursal;
        $datos["DireccionSucursal"]     = $sucursales->Direccion;

        // Buscamos en eAndreani el costo del envio segun los parametros enviados
        $datos["precio"]                = $this->cotizarEnvio($datos, Santex_Andreani_Helper_Data::WS_TIPO_COTIZAR);

        if ($datos["precio"] == 0) {
            return $texto  = Mage::helper('andreani')->__("Error en la conexión con Andreani. Por favor chequee los datos ingresados en la información de envio y vuelva a intentar.");
        } else {
            if($metodo != 'basico'){
                $texto  = Mage::getStoreConfig('carriers/andreanisucursal/description',Mage::app()->getStore()) . " {$sucursales->Descripcion} ({$sucursales->Direccion}). Estas a {$this->distancia_final_txt} {$this->mode} ({$this->duracion_final}).";
            }else{
                $texto  = Mage::getStoreConfig('carriers/andreanisucursal/description',Mage::app()->getStore()) . " {$sucursales->Descripcion} ({$sucursales->Direccion}).";
            }
        }

        $datos["CategoriaDistanciaId"]  = $this->envio->CategoriaDistanciaId;
        $datos["CategoriaPeso"]         = $this->envio->CategoriaPeso;

        Mage::getSingleton('core/session')->setAndreaniSucursal($datos);

        $rate->setMethodTitle($texto);

        if($request->getFreeShipping() == true || $request->getPackageQty() == $this->getFreeBoxes()) {
            $shippingPrice = '0.00';
            // cambiamos el titulo para indicar que el envio es gratis
            $direSucu  = " Sucursal: {$sucursales->Descripcion} ({$sucursales->Direccion}).";
            $rate->setMethodTitle(Mage::helper('andreani')->__('Envío gratis.') . $direSucu);
        } else {
            $shippingPrice = $this->getFinalPriceWithHandlingFee($datos["precio"]);
        }

        $shippingPrice = $shippingPrice + ($shippingPrice * Mage::getStoreConfig('carriers/andreanisucursal/regla') / 100);

        $rate->setPrice($shippingPrice);
        $rate->setCost($shippingPrice);

        return $rate;
    }
}
?>
