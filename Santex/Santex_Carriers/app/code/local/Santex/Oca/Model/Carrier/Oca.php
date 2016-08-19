<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Carrier_Oca extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    
    protected $ocaWs;
    protected $_code = 'oca';
    private $_error;
    private $valid_for_confirmation;
    
    public function isTrackingAvailable()
    {
        return true;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function getAllowedMethods()
    {
        return array('oca'=>$this->getConfigData('name'));
    }
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $this->getWs();
        $shipmentParams = array();
        $this->setRequest($request);
        
        $volume = 0;
        $freeBoxes = 0;

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                $full_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                
                if ($full_product->getTypeId() == 'simple') {
                    $volume += $item->getQty() * floatval($full_product->getVolume());
                }
                if ($item->getFreeShipping() && !$item->getProduct()->isVirtual()) {
                    $freeBoxes+=$item->getQty();

                }
            }
        }
        $this->setFreeBoxes($freeBoxes);
 
        $rate_result = Mage::getModel('shipping/rate_result');
        if ($this->getConfigData('type') == 'O') { // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') { // per item
            $shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
        } else {
            $shippingPrice = false;
        }
 
        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
 
        if ($shippingPrice !== false) {
            $model = Mage::getModel('oca/operatory');
            $operatories = $model->getCollection()
                    ->addFieldToFilter('active', array('eq' => 1));
                     
            $ocahelper = Mage::helper('oca');
            $cuit = $ocahelper->getCuit();
            $sender_zipcode = $ocahelper->formatZipcode($request->getPostcode());
            $receiver_zipcode = $ocahelper->formatZipcode($request->getDestPostcode());

            if ($cuit) {
                $shipmentParams = array(
                    'VolumenTotal'        => $volume,
                    'CodigoPostalOrigen'  => $sender_zipcode,
                    'CodigoPostalDestino' => $receiver_zipcode, //Cuando es envio a sucursal, se modifica con el CP de la misma (ver abajo)
                    'CantidadPaquetes'    => 1, 
                    'Cuit'                => $cuit,
                );

                foreach($operatories as $operatory) {
                    $shipmentParams['PesoTotal'] = floatval($request->getPackageWeight());

                    //TODO: see how to handle volume 
                    if ($volume == 0) {
                        $volume = Mage::getStoreConfig("carriers/oca/min_box_volume");
                    }

                    if ($operatory->getUsesIdci()) {
                        $branches = $this->ocaWs->getCentrosImposicionPorCP($receiver_zipcode);

                        foreach($branches as $branch) {
                            //each branch has a unique method_code.
                            $method_code = $operatory->getCode() . "_" . $branch->getCode();
                            $description = $operatory->getName() . " " . $branch->getFullDescription();
                            $shipmentParams['CodigoPostalDestino'] = $branch->getZipcode();
                            $this->_addRate($rate_result, $operatory, $shipmentParams, $method_code, $description);
                        }
                    } else {
                        $this->_addRate($rate_result, $operatory, $shipmentParams, $operatory->getCode());
                    }
                }
            }
        }
        return $rate_result;
    }

    public function getTrackingInfo($tracking)
    {
        $result = $this->getTracking($tracking);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }
    
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
        $result = Mage::getModel('shipping/tracking_result');
        foreach($trackings as $tracking){
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier($this->getConfigData('code'));
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setStatus(Mage::getModel('oca/epak_webservice')->getShipmentLastStatus($tracking));
            $status->setShippedDate(Mage::getModel('oca/epak_webservice')->getShipmentDate($tracking));
            //$status->setPopup(1);
            //$status->setUrl(Mage::getStoreConfig('oca/settings/tracking_url').$tracking);
            $result->append($status);
        }

        return $result;
    }
    
    protected function _addRate($rate_result, $operatory, $params, $operatory_code, $description = false) {
        $params['Operativa'] = $operatory->getCode();
        $this->getWs();
        $result = $this->ocaWs->tarifarEnvioCorporativo($params);
        if ($result == null) return;

        //TODO: VER tema impuestos
        if (Mage::getStoreConfig('tax/calculation/shipping_includes_tax')) {
            $result->Total = 1.21 * floatval($result->Total);
        }
        
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier('oca');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($operatory_code);

        $shippingPrice = (string) $result->Total;

        $_freeShipping = false;
        if ($this->getRequest()->getFreeShipping() === true || 
            $this->getRequest()->getPackageQty() == $this->getFreeBoxes()
        ) {
            $_freeShipping = true;
            $shippingPrice = '0.00';
        } elseif ($operatory->getPaysOnDestination()) {
            $shippingPrice = '0.00';
        }

        // in case caller needs to know
        if (!$_freeShipping) {
            $method->setRealPrice(round((string) $result->Total,2));
        } else {
            $method->setRealPrice($shippingPrice);
        }

        if ($description) {
            $method->setMethodTitle($description);
        } else {
            if ($operatory->getPaysOnDestination()) {
                $_method_title = Mage::helper('oca')->__('%s. Pay %s to courrier.', $operatory->getName(), Mage::helper('core')->currency($result->Total, true, false));
                $method->setMethodTitle($_method_title);
                $method->setMethodTitlePattern(Mage::helper('oca')->__('%s. Pay %%s to courrier.', $operatory->getName()));
            } else {
                $method->setMethodTitle($operatory->getName());
            }
        }
        
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        $rate_result->append($method);
    }

    protected function getWs() {
        if (!is_object($this->ocaWs)) {
            $this->ocaWs = Mage::getModel('oca/epak_webservice');
        }
        return $this->ocaWs;
    }
 }
